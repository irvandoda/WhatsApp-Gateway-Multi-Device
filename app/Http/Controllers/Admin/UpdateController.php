<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use ZipArchive;

class UpdateController extends Controller
{
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
	
    public function checkUpdate(Request $request)
	{
		if($request->complate == "1"){
			try {
				Artisan::call('migrate', [
					'--force' => true,
				]);
			} catch (\Exception $e) {

			}
			@Artisan::call('route:clear');
			@Artisan::call('cache:clear');
			@Artisan::call('config:clear');
			@Artisan::call('view:clear');
			@Artisan::call('chat:fix-user-ids');
			@file_get_contents(url('view-clear'));
			@file_get_contents(url('clear-cache'));
			@file_get_contents(url('migrate'));
			@file_get_contents(url('fix-numbers'));
		}
		if($request->user()->level != 'admin'){
			return redirect()->route('home');
		}
		
		$randomNumber = rand(10000, 99999);
        $serverProtocol = $this->getServerProtocol();
		$currentVersion = config('app.version');
		$checkUrl = "https://mpwa.onexgen.com/tools/check.php?v=$currentVersion&lang=".config('app.locale')."&rand=".$randomNumber;

		try {
			$response = Http::timeout(10)->get($checkUrl);
			$data = $response->json();

			if (isset($data['update_available']) && $data['update_available']) {
				$whats_new = base64_decode($data['whats_new']);
				return view('theme::pages.admin.update', ['updateAvailable' => $data['update_available'], 'newVersion' => $data['new_version'], 'updateSSL' => $data['ssl'], 'after' => $data['after'], 'before' => $data['before'], 'whatsNew' => $whats_new, 'serverProtocol' => $serverProtocol]);
			} else {
				return view('theme::pages.admin.update', ['updateAvailable' => false, 'newVersion' => '']);
			}

		} catch (\Exception $e) {
			return view('theme::pages.admin.update', ['updateAvailable' => false, 'newVersion' => '']);
		}
	}

    public function installUpdate(Request $request)
	{
		if($request->user()->level != 'admin'){
			return redirect()->route('home');
		}
		
		$randomNumber = rand(10000, 99999);
		
		$newVersion = $request->input('version');
		$sslInput = $request->input('ssl');
		$beforeInput = $request->input('before');
		$afterInput = $request->input('after');
		
		if($beforeInput === '1'){
			$beforeCommandUrl = "https://mpwa.onexgen.com/tools/".$newVersion."/command-before.txt?rand=".$randomNumber;
			$responseCommBefore = file_get_contents($beforeCommandUrl);
			if($responseCommBefore != ""){
				$commandFileAfter = Storage::path('command-before.php');
				Storage::put('command-before.php', $responseCommBefore);
				include $commandFileAfter;
				Storage::delete('command-before.php');
			}
		}
		
		if ($sslInput === 'ssl') {
			$serverJsContent = File::get(base_path('server.js'));

			$pattern = '/const serverOptions = \{[\s\S]*?\}[\s\S]*?const server = https\.createServer\(serverOptions, app\);/m';
			preg_match($pattern, $serverJsContent, $matches);
			$serverOptionsContent = isset($matches[0]) ? trim($matches[0]) : '';
			
			$newVersionUrl = "https://mpwa.onexgen.com/repo/update/linux/".$newVersion."/update-ssl.zip?rand=".$randomNumber;
		}else{
			$newVersionUrl = "https://mpwa.onexgen.com/repo/update/linux/".$newVersion."/update.zip?rand=".$randomNumber;
		}

		$zipFile = Storage::path('update.zip');
		$response = file_get_contents($newVersionUrl);

		if ($response != "") {
			Storage::put('update.zip', $response);
			
			if (class_exists('ZipArchive')) {
				$zip = new ZipArchive;
				if ($zip->open($zipFile) === TRUE) {
					$zip->extractTo(base_path());
					$zip->close();
				}
			}elseif (class_exists('PharData')) {
				$phar = new PharData($zipFile);
				$phar->extractTo(base_path());
			}else{
				return redirect()->route('update')->with('error', __('Failed to download update file. (zip-100)'));
			}

			Storage::delete('update.zip');
			
			if ($sslInput === 'ssl') {
				$serverJsPathNew = base_path('server.js');
				$serverJsContentNew = File::get($serverJsPathNew);
				$serverJsContentNew = str_replace("{{{SSL}}}", $serverOptionsContent, $serverJsContentNew);
				File::put($serverJsPathNew, $serverJsContentNew);
			}
			
			if($afterInput === '1'){
				$afterCommandUrl = "https://mpwa.onexgen.com/tools/".$newVersion."/command-after.txt?rand=".$randomNumber;
				$responseCommAfter = file_get_contents($afterCommandUrl);
				if($responseCommAfter != ""){
					$commandFileAfter = Storage::path('command-after.php');
					Storage::put('command-after.php', $responseCommAfter);
					include $commandFileAfter;
					Storage::delete('command-after.php');
				}
			}

			return redirect()->route('update')->with('status', __('Successfully updated to version (:version)', ['version' => $newVersion]));
		} else {
			return redirect()->route('update')->with('error', __('Failed to download update file.'));
		}
	}
}
