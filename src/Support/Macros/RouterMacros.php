<?php

namespace Juzaweb\Support\Macros;

use Illuminate\Support\Str;

class RouterMacros
{
    public function jwResource()
    {
        return function ($uri, $controller, $options = []) {
            $uriName = $options['name'] ?? str_replace('/', '.', $uri);
            $uriName = 'admin.' . $uriName;

            $this->get($uri, $controller . '@index')->name($uriName .'.index');
            $this->get($uri . '/create', $controller . '@create')->name($uriName . '.create');
            $this->get($uri . '/{id}/edit', $controller . '@edit')->name($uriName . '.edit')->where('id', '[0-9]+');
            $this->post($uri, $controller . '@store')->name($uriName . '.store');
            $this->put($uri . '/{id}', $controller . '@update')->name($uriName . '.update');
            $this->post($uri . '/bulk-actions', $controller . '@bulkActions')->name($uriName . '.bulk-actions');
        };
    }

    public function postTypeResource()
    {
        return function ($uri, $controller, $options = []) {
            $singular = Str::singular($uri);
            $this->jwResource($uri, $controller, $options);

            $this->jwResource(Str::singular($uri) . '/comments', '\Juzaweb\Http\Controllers\Backend\CommentController', $options);
            $this->get($singular . '/{taxonomy}/component-item', '\Juzaweb\Http\Controllers\Backend\TaxonomyController@getTagComponent');
            $this->jwResource($singular . '/{taxonomy}', '\Juzaweb\Http\Controllers\Backend\TaxonomyController', [
                'name' => Str::singular($uri) . '.taxonomy'
            ]);
        };
    }
}