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
 * Date: 5/31/2021
 * Time: 9:56 PM
 */

namespace Juzaweb\Cms\PostType\Datatables;

use Illuminate\Http\Request;
use Juzaweb\Cms\PostType\Models\Post;

class PostDatatable
{
    public function query(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $query = Post::query();

        if ($search) {
            $query->where(function ($subquery) use ($search) {
                $subquery->orWhere('name', 'like', '%'. $search .'%');
                $subquery->orWhere('description', 'like', '%'. $search .'%');
            });
        }

        if ($status) {
            $query->where('status', '=', $status);
        }

        return $query;
    }

    public function columns()
    {
        return [
            'thumbnail' => [
                'label' => trans('juzaweb::app.thumbnail'),
                'formatter' => [$this, 'thumbnailFormatter']
            ],
            'title' => [
                'label' => trans('juzaweb::app.title'),
                'formatter' => [$this, 'thumbnailFormatter']
            ],
            'created_at' => trans('juzaweb::app.created_at'),
            'status' => trans('juzaweb::app.status'),
        ];
    }

    public function thumbnailFormatter($value, $row, $index)
    {
        return '<img src="'. $row->thumb_url .'" class="w-100">';
    }

    public function nameFormatter($row)
    {

    }

    public function statusFormatter()
    {

    }
}