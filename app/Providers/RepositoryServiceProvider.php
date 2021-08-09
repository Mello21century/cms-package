<?php
/**
 * @package    juzawebcms/juzawebcms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://github.com/juzawebcms/juzawebcms
 * @license    MIT
 *
 * Created by The Anh.
 * Date: 5/25/2021
 * Time: 9:53 PM
 */

namespace Juzaweb\Cms\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            'Juzaweb\Cms\Repositories\PostRepository',
            'Juzaweb\Cms\Repositories\PostRepositoryEloquent'
        );

        $this->app->bind(
            'Juzaweb\Cms\Repositories\TaxonomyRepository',
            'Juzaweb\Cms\Repositories\TaxonomyRepositoryEloquent'
        );

        /*$this->app->bind(
            'Tadcms\System\Repositories\PageRepository',
            'Tadcms\System\Repositories\PageRepositoryEloquent'
        );


        $this->app->bind(
            'Tadcms\System\Repositories\UserRepository',
            'Tadcms\System\Repositories\UserRepositoryEloquent'
        );

        $this->app->bind(
            'Tadcms\System\Repositories\CommentRepository',
            'Tadcms\System\Repositories\CommentRepositoryEloquent'
        );

        $this->app->bind(
            'Tadcms\System\Repositories\MenuRepository',
            'Tadcms\System\Repositories\MenuRepositoryEloquent'
        );*/
    }
}
