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
 * Date: 5/25/2021
 * Time: 10:10 PM
 */

namespace Juzaweb\Cms\Backend\Http\Controllers;

use Juzaweb\Cms\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Juzaweb\Cms\Core\Traits\ResponseMessage;
use Juzaweb\Cms\PostType\PostType;

class BackendController extends Controller
{
    use ResponseMessage;

    public function callAction($method, $parameters)
    {
        do_action('backend.call_action', $method, $parameters);

        if (!file_exists(storage_path('app/installed'))) {
            if (!in_array(\Route::currentRouteName(), ['install', 'install.submit', 'install.submit.step'])) {
                return redirect()->route('install');
            }
        }

        if (config('juzaweb::app.demo', false) == 'true' && \Auth::id() != 1) {
            if (\request()->isMethod('post')) {
                if (\request()->is('admin-cp/*')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You cannot change the demo version',
                    ]);
                }

                if (\request()->is('account/change-password')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You cannot change the demo version',
                    ]);
                }
            }
        }

        $types = PostType::getPostTypes();
        foreach ($types as $key => $type) {
            add_action('post_type.'.$key.'.form.rigth', function ($model) use ($key) {
                echo view('juzaweb::components.taxonomies', [
                    'postType' => $key,
                    'model' => $model
                ])->render();
            });
        }

        return parent::callAction($method, $parameters);
    }

    protected function validateRequest($rules, Request $request, $attributeNames = null)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($attributeNames) {
            $validator->setAttributeNames($attributeNames);
        }

        if ($validator->fails()) {
            json_message($validator->errors()->all()[0], 'error');
        }
    }

    protected function addBreadcrumb(array $item, $name = 'admin')
    {
        add_filters($name . '_breadcrumb', function ($items) use ($item) {
            $items[] = $item;
            return $items;
        });
    }
}