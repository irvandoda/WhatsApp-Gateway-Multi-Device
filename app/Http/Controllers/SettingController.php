<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpClient\HttpClient;
use TomCan\AcmeClient\AcmeClient;
use TomCan\AcmeClient\Objects\Account;
use TomCan\AcmeClient\Account\AccountInterface;
use TomCan\AcmeClient\Order\OrderInterface;
use TomCan\AcmeClient\Authorization\AuthorizationInterface;
use TomCan\AcmeClient\Certificate\CertificateInterface;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->except(
            'activate_license',
            'install',
            'test_database_connection'
        );
    }
	
    public function index()
    {
		$getHost = $this->getDomain(env('WA_URL_SERVER'));
		$host = $getHost->host;
        $port = env('PORT_NODE');
		$allEnv = $this->getAllEnv();

		//$isConnected = $this->checkPort($host, $port);
		//$protocolMatch = $this->checkServerProtocol(env('WA_URL_SERVER'));
		
        return view('theme::pages.admin.settings', compact('host', 'port', 'allEnv'));
    }
	
	public function setRegistration(\Illuminate\Http\Request $request)
	{
		$enabled = $request->has('registration') ? 'true' : 'false';
		setEnv('REGISTERATION', $enabled);
		return back()->with('alert', [
			'type' => $enabled === 'true' ? 'success' : 'warning',
			'msg' => $enabled === 'true' ? __('Registration enabled.') : __('Registration disabled.')
		]);
	}
	
	public function cronJob()
    {
		$cron_path = $this->findCurlPath();
        return view('theme::pages.admin.cronjob', compact('cron_path'));
    }
	
	function findCurlPath() {
		$commonPaths = [
			'/usr/bin/curl',
			'/bin/curl',
			'/usr/local/bin/curl',
			'/opt/local/bin/curl',
			'/sbin/curl',
			'/usr/sbin/curl',
			'/usr/local/sbin/curl',
			'/snap/bin/curl',
			'/opt/bin/curl',
			'/usr/lib/curl',
			'/var/lib/curl',
			'/etc/alternatives/curl',
			'/opt/curl/bin/curl',
			'/usr/local/curl/bin/curl',
			'/usr/local/opt/curl/bin/curl',
			'/usr/local/bin/curl/bin/curl',
			'/opt/homebrew/bin/curl',
			'/mnt/c/Windows/System32/curl.exe',
			'/opt/cpanel/ea-php82/root/usr/bin/curl',
			'/opt/cpanel/ea-php83/root/usr/bin/curl',
			'/opt/cpanel/ea-php84/root/usr/bin/curl',
			'/opt/alt/php82/usr/bin/curl',
			'/opt/alt/php83/usr/bin/curl',
			'/opt/alt/php84/usr/bin/curl',
			'/usr/local/cpanel/3rdparty/bin/curl',
			'C:\\xampp\\php\\curl.exe',
			'C:\\xampp\\curl\\curl.exe',
			'C:\\Windows\\System32\\curl.exe',
			'C:\\Windows\\curl.exe',
			'C:\\Program Files\\Git\\mingw64\\bin\\curl.exe',
			'C:\\Program Files\\cURL\\bin\\curl.exe',
			'C:\\Program Files\\cURL\\curl.exe',
			'C:\\Program Files (x86)\\curl\\bin\\curl.exe',
			'C:\\Program Files (x86)\\cURL\\curl.exe',
			'C:\\Tools\\curl\\curl.exe',
			'C:\\ProgramData\\chocolatey\\bin\\curl.exe',
			'C:\\cygwin64\\bin\\curl.exe',
			'C:\\MSYS2\\usr\\bin\\curl.exe',
		];

		foreach ($commonPaths as $path) {
			if (@file_exists($path) && @is_executable($path)) {
				return $path;
			}
		}

		return 'curl';
	}
	
	public function getDomain($url) {
		$parsedUrl = parse_url($url);

		$host = $parsedUrl['host'] ?? '';
		$port = $parsedUrl['port'] ?? null;

		return (object)[
			'host' => $host,
			'port' => $port,
		];
	}
	
	public function setEnvAll(Request $request)
	{
		$InputAll = $request->except('_token');
		foreach ($InputAll as $key => $value) {
			setEnv($key, $value);
		}

		return back()->with('alert', ['type' => 'success', 'msg' => __('Environment variables updated successfully.')]);
	}
    
    public function generateSslCertificate(Request $request)
    {
		$domain = $request->input('domain');
		$email = $request->input('email');
		
        $httpClient = HttpClient::create();
        $acmeClient = new AcmeClient($httpClient, 'https://acme-staging-v02.api.letsencrypt.org/directory');
        $account = new Account($email, null, null);

        $acmeClient->getAccount($account);

        Storage::put('account.json', json_encode([
            'email' => $account->getEmail(),
            'url' => $account->getUrl(),
            'key' => $account->getKey()
        ]));

        $accountData = json_decode(Storage::get('account.json'), true);
        $account = new Account($accountData['email'], $accountData['url'], $accountData['key']);
        $acmeClient->getAccount($account);

        $order = $acmeClient->createOrder([$domain]);
        $authorizations = $acmeClient->authorize($order);

        $challenges = [];
        foreach ($authorizations as $authorization) {
            foreach ($authorization->getChallenges() as $challenge) {
                if ($challenge->getType() === 'http-01') {
                    $challenges[] = $challenge;
                     $path = base_path('.well-known/acme-challenge/' . $challenge->getToken());
                    if (!File::exists(dirname($path))) {
                        File::makeDirectory(dirname($path), 0755, true);
                    }
                    File::put($path, $challenge->getValue());
                }
            }
        }
		
		sleep(5);

        $result = $acmeClient->validate($authorizations, $challenges);

        if ($result) {
            $cert = $acmeClient->finalize($order);
            File::put(base_path('cert.pem'), $cert->getCertificate());
            File::put(base_path('key.pem'), $cert->getKey());
			File::put(base_path('csr.pem'), $cert->getCsr());
			
			if (File::exists(base_path('cert.pem'))) {
				$webPhpPath = base_path('routes/web.php');
				$webPhpContent = File::get($webPhpPath);
				$serverJsPath = base_path('server.js');
				$serverJsContent = File::get($serverJsPath);
				
				if (strpos($webPhpContent, "URL::forceScheme") === false) {
					File::put(base_path('routes/web.php.bak'), $webPhpContent);
					$webPhpContent = str_replace("?>", "URL::forceScheme('https');\n?>", $webPhpContent);
					File::put($webPhpPath, $webPhpContent);
				}
				
				if ($this->getServerProtocol() === "https"){
					File::put(base_path('server.js.bak'), $serverJsContent);
					$pattern = '/const serverOptions = \{[\s\S]*?\}[\s\S]*?const server = https\.createServer\(serverOptions, app\);/m';
					preg_match($pattern, $serverJsContent, $matches);
					$serverOptionsContent = isset($matches[0]) ? trim($matches[0]) : '';
					$serverOptionsContent = str_replace($serverOptionsContent, "const serverOptions = {\n  key: fs.readFileSync('key.pem'),\n  cert: fs.readFileSync('cert.pem')\n}\n\nconst express = require(\"express\");\nconst app = express();\nconst https = require(\"https\");\nconst server = https.createServer(serverOptions, app);", $serverJsContent);
					File::put($serverJsPath, $serverOptionsContent);
				} else {
					File::put(base_path('server.js.bak'), $serverJsContent);
					$pattern = '/const express = [\s\S]*?const server = http\.createServer\(app\);/m';
					preg_match($pattern, $serverJsContent, $matches);
					$serverOptionsContent = isset($matches[0]) ? trim($matches[0]) : '';
					$serverOptionsContent = str_replace($serverOptionsContent, "const serverOptions = {\n  key: fs.readFileSync('key.pem'),\n  cert: fs.readFileSync('cert.pem')\n}\n\nconst express = require(\"express\");\nconst app = express();\nconst https = require(\"https\");\nconst server = https.createServer(serverOptions, app);", $serverJsContent);
					File::put($serverJsPath, $serverOptionsContent);
				}
				
				$replaceAPP_URL = str_replace(array('https', 'http'), "https", env('APP_URL'));
				$replaceWA_URL_SERVER = str_replace(array('https', 'http'), "https", env('WA_URL_SERVER'));
				setEnv('APP_URL', $replaceAPP_URL);
				setEnv('WA_URL_SERVER', $replaceWA_URL_SERVER);
				
			} else {
				return back()->with('alert', ['type' => 'danger', 'msg' => __('Failed to generate SSL certificate.')]);
			}

            return back()->with('alert', ['type' => 'success', 'msg' => __('SSL certificate generated successfully, please restart NodeJS')]);
        }

        return back()->with('alert', ['type' => 'danger', 'msg' => __('Failed to generate SSL certificate.')]);
    }
	
	public function getServerProtocol()
	{
		$serverJsContent = File::get(base_path('server.js'));
			if (strpos($serverJsContent, 'require("https")') !== false) {
					return 'https';
			}
			if (strpos($serverJsContent, 'require("http")') !== false) {
				return 'http';
			}

		return 'unknown';
	}

    public function setServer(Request $request)
    {
        $request->validate([
            'typeServer' => ['required'],
            'portnode' => ['required'],
            'urlnode' => ['required_if:typeServer,other', 'nullable', 'url'],
        ]);
        $urlnode =
            $request->typeServer === 'other'
            ? $request->urlnode . ':' . $request->portnode
            : ($request->typeServer === 'hosting'
                ? url('/')
                : 'http://localhost:' . $request->portnode);
        setEnv('TYPE_SERVER', $request->typeServer);
        setEnv('PORT_NODE', $request->portnode);
        setEnv('WA_URL_SERVER', $urlnode);
        return back()->with('alert', [
            'type' => 'success',
            'msg' => __('Success Update configuration!'),
        ]);
    }

    public function activate_license(Request $request)
    {
        $push = "Magd Almuntaser";
		return json_decode($push);
    }

    public function test_database_connection(Request $request)
    {
        $data = json_decode(json_encode($request->database));
        $error_message = null;
        try {
            $db = new \mysqli(
                $data->host,
                $data->username,
                $data->password,
                $data->database
            );
            $error_message = $db->connect_errno
                ? 'Connection Failed .' . $db->connect_error
                : $error_message;
        } catch (\Throwable $th) {
            $error_message = __('Connection failed');
        }
        return response()->json([
            'status' => $error_message ?? 'Success',
            'error' => $error_message === null ? false : true,
        ]);
    }
	
	public function getAllEnv()
    {
		$allEnv = collect($_ENV)->all();
		return $allEnv;
	}

    public function install(Request $request)
    {
        if (env('APP_INSTALLED') === true) {
            return redirect(route('index'));
        }
        
        if ($request->method() === 'POST') {
            try {
                // Validate input
                $request->validate([
                    'database.*' => 'required|string',
                    'admin.username' => 'required',
                    'admin.email' => 'required|email',
                    'admin.password' => 'required|max:255',
                    'typeServer' => 'required',
                    'portnode' => 'required',
                    'urlnode' => 'nullable|url|required_if:typeServer,other',
                ]);
                
                $urll = $request->getSchemeAndHttpHost();
                $type = $request->input('typeServer');
                $port = $request->input('portnode');
                $urlnode = match($type) {
                    'other' => $request->input('urlnode') . ':' . $port,
                    'hosting' => $urll,
                    default => 'http://localhost:' . $port,
                };

                // Test database connection
                $db_params = $request->input('database');
                Config::set(
                    'database.connections.mysql',
                    array_merge(config('database.connections.mysql'), $db_params)
                );
                
                try {
                    DB::connection()->getPdo();
                } catch (\Exception $e) {
                    Log::error('Database connection failed: ' . $e->getMessage());
                    return back()->withErrors(['Database' => 'Database connection failed: ' . $e->getMessage()])->withInput();
                }

                // Run migrations
                try {
                    Artisan::call('migrate:fresh', ['--force' => true]);
                    Artisan::call('db:seed', ['--force' => true]);
                    Artisan::call('cache:clear');
                } catch (\Exception $e) {
                    Log::error('Migration failed: ' . $e->getMessage());
                    return back()->withErrors(['Migration' => 'Migration failed: ' . $e->getMessage()])->withInput();
                }
                
                // Set permissions
                @chmod(base_path('node_modules/cwebp-bin/vendor/cwebp'), 0755);

                // Update .env
                $env = [
                    'DB_HOST' => $db_params['host'],
                    'DB_DATABASE' => $db_params['database'],
                    'DB_USERNAME' => $db_params['username'],
                    'DB_PASSWORD' => $db_params['password'],
                    'APP_URL' => $urll,
                    'WA_URL_SERVER' => $urlnode,
                    'PORT_NODE' => $port,
                    'TYPE_SERVER' => $type,
                    'APP_INSTALLED' => 'true',
                ];
                
                if ($request->input('licensekey') != null) {
                    $env['LICENSE_KEY'] = $request->input('licensekey');
                }
                if ($request->input('buyeremail') != null) {
                    $env['BUYER_EMAIL'] = $request->input('buyeremail');
                }

                foreach ($env as $k => &$v) {
                    setEnv($k, $v);
                }

                // Create admin user
                if (!($user = User::where('email', $request->input('admin.email'))->first())) {
                    $user = new User();
                    $user->username = $request->input('admin.username');
                    $user->email = $request->input('admin.email');
                    $user->password = Hash::make($request->input('admin.password'));
                    $user->email_verified_at = date('Y-m-d');
                    $user->level = 'admin';
                    $user->active_subscription = 'lifetime';
                    $user->limit_device = 10;
                    $user->chunk_blast = 0;
                    $user->save();
                }
                
                // Create storage link
                try {
                    if (function_exists('symlink')) {
                        @Artisan::call('storage:link');
                    }
                } catch (\Exception $e) {
                    // Ignore symlink errors
                }
                
                // Login and redirect
                Auth::loginUsingId($user->id, true);
                return redirect()->route('home');
                
            } catch (\Exception $e) {
                Log::error('Installation failed: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
                return back()->withErrors(['Installation' => 'Installation failed: ' . $e->getMessage()])->withInput();
            }
        }

        // GET request - show installation form
        $requirements = [
            'php' => ['version' => "8.2", 'current' => phpversion()],
            'php_extensions' => [
                'curl' => false,
                'fileinfo' => false,
                'intl' => false,
                'json' => false,
                'mbstring' => false,
                'openssl' => false,
                'mysqli' => false,
                'zip' => false,
                'ctype' => false,
                'dom' => false,
            ],
        ];

        $php_loaded_extensions = get_loaded_extensions();

        foreach ($requirements['php_extensions'] as $name => &$enabled) {
            $enabled = in_array($name, $php_loaded_extensions);
        }

        return view('theme::install', [
            'requirements' => $requirements,
        ]);
    }
}
?>