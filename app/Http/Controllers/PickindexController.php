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
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class PickindexController extends Controller
{
    public function editSettings()
	{
		$languages = LaravelLocalization::getSupportedLocales();
		$translations = [];
		$cssVariables = [];

		foreach ($languages as $lang => $properties) {
			$path = resource_path("lang/index/{$lang}.json");
			if (File::exists($path)) {
				$translations[$lang] = json_decode(File::get($path), true);
			}
		}

		$variablesToUpdate = [
			'--bs-primary',
			'--bs-footer-bg',
			'--bs-footer-alt-bg',
		];
		$cssFilePath = public_path('index/lezir/css/style.ltr.min.css');
		if (File::exists($cssFilePath)) {
			$cssContent = File::get($cssFilePath);
			foreach ($variablesToUpdate as $var) {
				if (preg_match("/{$var}:\s*(.*?);/", $cssContent, $matches)) {
					$cssVariables[$var] = trim($matches[1]);
				}
			}
		}

		$configSettings = [];
		$configRaw = File::get(config_path('config.php'));
		preg_match_all("/'([^']+)'\s*=>\s*(.*?),\s*$/m", $configRaw, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {
			$key = $match[1];
			$raw = trim($match[2]);

			if (preg_match("/^'(.*)'\s*\.\s*config\('app\.version'\)\s*\.\s*'(.*)'$/", $raw, $parts)) {
				$before = $parts[1];
				$after  = $parts[2];
				$configSettings[$key] = $before . '{version}' . $after;
			}
			elseif (preg_match("/^'(.*)'\s*\.\s*config\('app\.version'\)$/", $raw, $parts)) {
				$before = $parts[1];
				$configSettings[$key] = $before . '{version}';
			}
			elseif (preg_match("/^config\('app\.version'\)\s*\.\s*'(.*)'$/", $raw, $parts)) {
				$after = $parts[1];
				$configSettings[$key] = '{version}' . $after;
			}
			elseif ($raw === 'true' || $raw === 'false') {
				$configSettings[$key] = $raw === 'true';
			}
			elseif (preg_match("/^'(.*)'$/", $raw, $valMatch)) {
				$configSettings[$key] = $valMatch[1];
			}
		}

		return view('theme::pages.admin.index', compact('languages', 'translations', 'cssVariables', 'configSettings'));
	}

	public function enableIndex(Request $request)
    {
		$enableindex = $request->input('enableindex');
		@setEnv('ENABLE_INDEX', $enableindex);
		
		return redirect()->route('admin.index.edit')->with('alert' , ['type' => 'success', 'msg' => __('Settings updated successfully.')]);
	}
	
	public function updateColor(Request $request)
    {
        $colors = $request->input('colors', []);

		$variablesToUpdate = [
			'--bs-primary',
			'--bs-footer-bg',
			'--bs-footer-alt-bg',
		];
		
		foreach (['style.rtl.min.css', 'style.ltr.min.css'] as $fileName) {
			$filePath = public_path("index/lezir/css/{$fileName}");
			if (File::exists($filePath)) {
				$cssContent = File::get($filePath);

				foreach ($variablesToUpdate as $var) {
					if (isset($colors[$var])) {
						$cssContent = preg_replace(
							"/{$var}:.*?;/",
							"{$var}: {$colors[$var]};",
							$cssContent
						);
					}
				}

				File::put($filePath, $cssContent);
			}
		}

        return redirect()->route('admin.index.edit')->with('alert' , ['type' => 'success', 'msg' => __('Settings updated successfully.')]);
    }

    public function updateSettings(Request $request)
    {
        $translations = $request->input('translations', []);
        foreach ($translations as $lang => $texts) {
            $path = resource_path("lang/index/{$lang}.json");
            File::put($path, json_encode($texts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
		
        return redirect()->route('admin.index.edit')->with('alert' , ['type' => 'success', 'msg' => __('Settings updated successfully.')]);
    }
	
	public function updateConfigOptions(Request $request)
	{
		$items     = $request->input('config', []);
		$configPath = config_path('config.php');
		$content   = File::get($configPath);

		foreach ($items as $key => $value) {
			if ($value === 'true') {
				$code = 'true';
			} elseif ($value === 'false') {
				$code = 'false';
			} else {
				if (str_contains($value, '{version}')) {
					$parts  = explode('{version}', $value);
					$before = $parts[0];
					$after  = $parts[1] ?? '';

					$code = var_export($before, true)
						  . " . config('app.version')";
					if ($after !== '') {
						$code .= " . " . var_export($after, true);
					}
				} else {
					$code = var_export($value, true);
				}
			}

			$content = preg_replace(
				"/('{$key}'\s*=>\s*)(.*?)(,)/",
				'$1' . $code . '$3',
				$content
			);
		}

		File::put($configPath, $content);
		return redirect()->route('admin.index.edit')->with('alert' , ['type' => 'success', 'msg' => __('Settings updated successfully.')]);
	}

}
