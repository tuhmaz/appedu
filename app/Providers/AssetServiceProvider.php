<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class AssetServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Add versioning to assets
        View::composer('*', function ($view) {
            $version = config('app.asset_version', time());
            $view->with('assetVersion', $version);
        });

        // Add custom Blade directive for loading CSS
        Blade::directive('loadCSS', function ($expression) {
            return "<?php echo '<link rel=\"stylesheet\" href=\"' . $expression . '?v=' . config('app.asset_version', time()) . '\">' ?>";
        });

        // Add custom Blade directive for loading JS
        Blade::directive('loadJS', function ($expression) {
            return "<?php echo '<script src=\"' . $expression . '?v=' . config('app.asset_version', time()) . '\" defer></script>' ?>";
        });

        // Add custom Blade directive for lazy loading images
        Blade::directive('lazyImage', function ($expression) {
            $args = explode(',', $expression);
            $src = trim($args[0]);
            $alt = isset($args[1]) ? trim($args[1]) : '""';
            $class = isset($args[2]) ? trim($args[2]) : '""';
            
            return "<?php echo '<img loading=\"lazy\" src=\"' . $src . '\" alt=\"' . $alt . '\" class=\"' . $class . '\">'; ?>";
        });
    }
}
