<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

use Illuminate\Support\Facades\Http;

function str_extract($str, $pattern, $get = null, $default = null)
{
    $result = [];
    preg_match($pattern, $str, $matches);
    preg_match_all('/(\(\?P\<(?P<name>.+)\>\.\+\)+)/U', $pattern, $captures);
    $names = $captures['name'] ?? [];
    foreach ($names as $name) {
        $result[$name] = $matches[$name] ?? null;
    }
    return $get ? $result[$get] ?? $default : $result;
}

function wrap_str($str = '', $first_delimiter = "'", $last_delimiter = null)
{
    if (!$last_delimiter) {
        return $first_delimiter . $str . $first_delimiter;
    }

    return $first_delimiter . $str . $last_delimiter;
}

function getExtensionImageFromUrl($url)
{
    $url = explode('.', $url);
    $extension = end($url);
    return $extension;
}

function clearCacheNode($request = false)
{
    try {
		$data = [
            'body' => $request,
        ];
        Http::withOptions(['verify' => false])
            ->asForm()
            ->post(env('WA_URL_SERVER') . '/backend-clearCache', $data);
        return true;
    } catch (\Throwable $th) {
        return false;
    }
}

function setEnv(string $key, string $value)
{
    $env = array_reduce(
        file(base_path('.env'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES),
        function ($carry, $item) {
            list($key, $val) = explode('=', $item, 2);

            $carry[$key] = $val;

            return $carry;
        },
        []
    );
    $env[$key] = $value;
    foreach ($env as $k => &$v) {
        $v = "{$k}={$v}";
    }

    file_put_contents(base_path('.env'), implode("\r\n", $env));
}

function backWithFlash($type, $message)
{
    return redirect()->back()->with('alert', ['type' => $type, 'msg' => $message]);
}

function redirectWithFlash($type, $message, $url)
{
    return redirect($url)->with('alert', ['type' => $type, 'msg' => $message]);
}

function asset_index($path, $secure = null)
{
	$index = env('THEME_INDEX') ?? 'lezir';
    return url('index/' . $index . '/' . ltrim($path, '/'));
}

function __index($key, $replace = [], $locale = null)
{
    $langPath = resource_path('lang/index');

    $locale = $locale ?? app()->getLocale();

    $translator = new \Illuminate\Translation\FileLoader(new \Illuminate\Filesystem\Filesystem, $langPath);
    $translator = new \Illuminate\Translation\Translator($translator, $locale);

    return $translator->get($key, $replace);
}
