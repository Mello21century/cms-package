<div class="form-group">
    <label class="col-form-label">{{ trans('juzaweb::app.url') }}</label>
    <input type="text" class="form-control menu-data" data-name="link" placeholder="http://" autocomplete="off" value="{{ $item->link }}">
</div>

<div class="form-group">
    <label class="col-form-label">{{ trans('juzaweb::app.link_text') }}</label>
    <input type="text" class="form-control change-name menu-data" data-name="label" autocomplete="off" required value="{{ $item->label }}">
</div>
