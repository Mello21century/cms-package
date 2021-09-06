<?php

namespace Juzaweb\Cms\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class HookBladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * Adds a directive in Blade for actions
         */
        Blade::directive('do_action', function ($expression) {
            return "<?php app(\Juzaweb\Cms\Contracts\EventyContract::class)->action({$expression}); ?>";
        });

        /*
         * Adds a directive in Blade for filters
         */
        Blade::directive('apply_filters', function ($expression) {
            return "<?php echo app(\Juzaweb\Cms\Contracts\EventyContract::class)->filter({$expression}); ?>";
        });
        
    }
}
