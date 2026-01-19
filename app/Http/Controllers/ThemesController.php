<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class ThemesController extends Controller
{
    public function index()
    {
        $themes = [];
        $themesPath = public_path('themes');
        if (File::exists($themesPath)) {
            foreach (File::directories($themesPath) as $folder) {
                $infoFile = $folder . '/info.json';
                $screenshot = public_path('themes/' . basename($folder) . '/screenshot.jpg');
                if (File::exists($infoFile)) {
                    $info = json_decode(File::get($infoFile), true);
                    $themes[] = [
                        'name'          => $info['name']          ?? 'Unknown',
                        'version'       => $info['version']       ?? 'Unknown',
                        'author'        => $info['author']        ?? 'Unknown',
                        'compatibility' => $info['compatibility'] ?? '',
                        'folder'        => basename($folder),
						'website' 		=> $info['website'] ?? 'Unknown',
                        'screenshot'    => File::exists($screenshot)
                                           ? '/themes/' . basename($folder) . '/screenshot.jpg'
                                           : null,
                    ];
                }
            }
			usort($themes, function ($a, $b) {
				return ($a['folder'] == env('THEME_NAME')) ? -1 : (($b['folder'] == env('THEME_NAME')) ? 1 : 0);
			});
        }

        $indexThemes = [];
        $indexPath = public_path('index');
        if (File::exists($indexPath)) {
            foreach (File::directories($indexPath) as $folder) {
                $infoFile       = $folder . '/info.json';
                $screenshotPath = $folder . '/screenshot.jpg';
                $screenshot     = File::exists($screenshotPath)
                                   ? '/index/' . basename($folder) . '/screenshot.jpg'
                                   : null;
                $info = [];
                if (File::exists($infoFile)) {
                    $info = json_decode(File::get($infoFile), true) ?? [];
                }
                $indexThemes[] = [
                    'name'       => $info['name']    ?? basename($folder),
                    'version'    => $info['version'] ?? '1.0.0',
                    'author'     => $info['author']  ?? 'Unknown',
                    'folder'     => basename($folder),
                    'screenshot' => $screenshot ?? 'https://placehold.co/314x138?text=No+Theme+Photo',
                ];
            }
			usort($indexThemes, function ($a, $b) {
				return ($a['folder'] == env('THEME_INDEX')) ? -1 : (($b['folder'] == env('THEME_INDEX')) ? 1 : 0);
			});
        }

        $clearLocalStorage = session('clearLocalStorage', null);
        session()->forget('clearLocalStorage');
		$currentVersion = config('app.version');

        return view('theme::pages.admin.themes', compact(
            'themes',
            'indexThemes',
            'clearLocalStorage',
			'currentVersion'
        ));
    }

    public function activeTheme(Request $request)
    {
        if ($request->user()->level !== 'admin') {
            return redirect()->route('home');
        }

        Artisan::call('route:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        session(['clearLocalStorage' => '<script>localStorage.clear();</script>']);

        setEnv('THEME_NAME', $request->name);

        return redirect()->route('admin.manage-themes')
                         ->with('status', __('Theme activated successfully'));
    }

    public function activeIndexTheme(Request $request)
    {
        if ($request->user()->level !== 'admin') {
            return redirect()->route('home');
        }

        Artisan::call('route:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        session(['clearLocalStorage' => '<script>localStorage.clear();</script>']);

        setEnv('THEME_INDEX', $request->name);

        return redirect()->route('admin.manage-themes')
                         ->with('status', __('Home page theme activated successfully'));
    }

    public function deleteTheme(Request $request)
    {
        if ($request->user()->level !== 'admin') {
            return redirect()->route('home');
        }

        File::deleteDirectory(public_path('themes/' . $request->folder));
        File::deleteDirectory(resource_path('themes/' . $request->folder));

        return redirect()->route('admin.manage-themes')
                         ->with('status', __('Theme deleted successfully'));
    }

    public function deleteIndexTheme(Request $request)
    {
        if ($request->user()->level !== 'admin') {
            return redirect()->route('home');
        }

        File::deleteDirectory(public_path('index/' . $request->folder));
        File::deleteDirectory(resource_path('index/' . $request->folder));

        return redirect()->route('admin.manage-themes')
                         ->with('status', __('Home page theme deleted successfully'));
    }
}
