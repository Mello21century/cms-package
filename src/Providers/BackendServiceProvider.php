<?php
/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzawebcms/juzawebcms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://github.com/juzawebcms/juzawebcms
 * @license    MIT
 *
 * Created by JUZAWEB.
 * Date: 6/19/2021
 * Time: 6:31 PM
 */

namespace Juzaweb\Providers;

use Juzaweb\Actions\EnqueueStyleAction;
use Juzaweb\Actions\MenuAction;
use Juzaweb\Support\ServiceProvider;
use Juzaweb\Http\Middleware\Admin;
use Illuminate\Routing\Router;
use Juzaweb\Support\Macros\RouterMacros;

class BackendServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->bootMiddlewares();
        $this->bootPublishes();
        $this->registerAction([
            MenuAction::class,
            EnqueueStyleAction::class
        ]);
    }

    public function register()
    {
        $this->registerRouteMacros();
    }

    protected function bootMiddlewares()
    {
        $this->app['router']->aliasMiddleware('admin', Admin::class);
    }

    protected function bootPublishes()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'juzaweb');
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/juzaweb'),
        ], 'juzaweb_views');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'juzaweb');
        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/juzaweb'),
        ], 'juzaweb_lang');

        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/juzaweb'),
        ], 'juzaweb_assets');
    }

    protected function registerRouteMacros()
    {
        Router::mixin(new RouterMacros());
    }
}