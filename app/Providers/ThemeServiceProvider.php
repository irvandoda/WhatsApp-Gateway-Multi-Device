<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
	{
		$theme = env('THEME_NAME') ?? 'mpwa';
		$index = env('THEME_INDEX') ?? 'lezir';

		$componentsPath = resource_path("themes/{$theme}/views/components");
		$componentsIndexPath = resource_path("index/{$index}/views/components");

		foreach (glob($componentsPath . '/*.blade.php') as $component) {
			$componentName = basename($component, '.blade.php');
			Blade::component('theme::components.' . $componentName, $componentName);
		}
		
		foreach (glob($componentsIndexPath . '/*.blade.php') as $componentIndex) {
			$componentNameIndex = basename($componentIndex, '.blade.php');
			Blade::component('index::components.' . $componentNameIndex, 'index-' . $componentNameIndex);
		}

		View::addNamespace('theme', resource_path('themes/' . $theme . '/views'));
		View::addNamespace('index', resource_path('index/' . $index . '/views'));

	}
}
