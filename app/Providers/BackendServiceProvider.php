<?php
/**
 * MYMO CMS - Free Laravel CMS
 *
 * @package    juzawebcms/juzawebcms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://github.com/juzawebcms/juzawebcms
 * @license    MIT
 *
 * Created by The Anh.
 * Date: 6/19/2021
 * Time: 6:31 PM
 */

namespace Juzaweb\Cms\Providers;

use Illuminate\Support\ServiceProvider;
use Juzaweb\Cms\Http\Middleware\Admin;
use Illuminate\Routing\Router;
use Juzaweb\Cms\Macros\RouterMacros;
use Juzaweb\Cms\Facades\HookAction;

class BackendServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->bootMiddlewares();
        $this->bootPublishes();
        HookAction::loadActionForm(__DIR__ . '/../../actions');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'juzaweb');

        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'juzaweb');
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
        $this->publishes([
            __DIR__ . '/../../../assets' => public_path('juzaweb'),
        ], 'juzaweb_assets');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('vendor/juzawebcms'),
        ], 'juzaweb_lang');
    }

    protected function registerRouteMacros()
    {
        Router::mixin(new RouterMacros());
    }
}