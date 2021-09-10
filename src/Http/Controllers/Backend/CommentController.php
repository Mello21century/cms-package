<?php

namespace Juzaweb\Http\Controllers\Backend;

use Juzaweb\Http\Controllers\BackendController;
use Juzaweb\Http\Datatable\CommentDatatable;
use Juzaweb\Traits\ResourceController;
use Juzaweb\Models\Comment;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class CommentController extends BackendController
{
    use ResourceController {
        ResourceController::getDataForIndex as traitDataForIndex;
    }

    protected $viewPrefix = 'juzaweb::backend.comment';

    protected function validator(array $attributes)
    {
        $statuses = array_keys(Comment::allStatuses());

        return [
            'email' => 'required|email',
            'name' => 'nullable',
            'website' => 'nullable',
            'content' => 'required',
            'status' => 'required|in:' . implode(',', $statuses),
        ];
    }

    protected function getModel()
    {
        return Comment::class;
    }

    protected function getTitle()
    {
        return trans('juzaweb::app.comments');
    }

    protected function getDataTable()
    {
        $dataTable = new CommentDatatable();
        $dataTable->mountData($this->getPostType());
        return $dataTable;
    }

    protected function getDataForIndex()
    {
        $postType = $this->getPostType();
        $data = $this->traitDataForIndex();
        $data['postType'] = $postType;
        return $data;
    }

    protected function getPostType()
    {
        $split = explode('.', Route::currentRouteName());
        return Str::plural($split[count($split) - 3]);
    }
}
