<?php

namespace Juzaweb\Cms\Http\Controllers\Backend;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Juzaweb\Cms\Http\Controllers\BackendController;
use Juzaweb\Cms\Models\Page;

class PageController extends BackendController
{
    public function index()
    {
        return view('jw_theme::backend.page.index', [
            'title' => trans('juzaweb::app.pages')
        ]);
    }
    
    public function getDataTable(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        
        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'desc');
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        
        $query = Page::query();
        
        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->orWhere('name', 'like', '%'. $search .'%');
                $q->orWhere('description', 'like', '%'. $search .'%');
            });
        }
        
        if (!is_null($status)) {
            $query->where('status', '=', $status);
        }
        
        $count = $query->count();
        $query->orderBy($sort, $order);
        $query->offset($offset);
        $query->limit($limit);
        $rows = $query->get();
        
        foreach ($rows as $row) {
            $row->thumb_url = $row->getThumbnail();
            $row->edit_url = route('admin.pages.edit', [$row->id]);
        }
        
        return response()->json([
            'total' => $count,
            'rows' => $rows
        ]);
    }

    public function create()
    {
        $this->addBreadcrumb([
            'title' => trans('juzaweb::app.pages'),
            'url' => route('admin.pages.index')
        ]);

        $model = new Page();

        return view('jw_theme::backend.page.form', [
            'model' => $model,
            'title' => trans('juzaweb::app.add_new')
        ]);
    }
    
    public function edit($id)
    {
        $this->addBreadcrumb([
            'title' => trans('juzaweb::app.pages'),
            'url' => route('admin.pages.index')
        ]);

        $model = Page::firstOrNew(['id' => $id]);

        return view('jw_theme::backend.page.form', [
            'model' => $model,
            'title' => $model->name ?: trans('juzaweb::app.add_new')
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'status' => 'required|in:0,1',
            'thumbnail' => 'nullable|string|max:250',
        ], [], [
            'name' => trans('juzaweb::app.name'),
            'status' => trans('juzaweb::app.status'),
            'thumbnail' => trans('juzaweb::app.thumbnail'),
        ]);
        
        $model = Page::firstOrNew(['id' => $request->id]);
        $model->fill($request->all());
        $model->save();

        return $this->success([
            'message' => trans('juzaweb::app.successfully'),
            'redirect' => route('admin.pages.index')
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'status' => 'required|in:0,1',
            'thumbnail' => 'nullable|string|max:250',
        ], [], [
            'name' => trans('juzaweb::app.name'),
            'status' => trans('juzaweb::app.status'),
            'thumbnail' => trans('juzaweb::app.thumbnail'),
        ]);

        $model = Page::findOrFail($id);
        $model->fill($request->all());
        $model->save();

        return $this->success([
            'message' => trans('juzaweb::app.successfully'),
        ]);
    }
    
    public function bulkActions(Request $request)
    {
        $request->validate([
            'ids' => 'required',
            'action' => 'required',
        ], [], [
            'ids' => trans('juzaweb::app.pages')
        ]);

        $ids = $request->post('ids');
        $action = $request->post('action');

        try {
            DB::beginTransaction();
            switch ($action) {
                case 'delete':
                    Page::destroy($ids);
                    break;
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->error([
                'message' => $exception->getMessage()
            ]);
        }

        return $this->success([
            'message' => trans('juzaweb::app.successfully')
        ]);
    }
}
