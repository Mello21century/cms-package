<?php

namespace Juzaweb\Cms\PostType\Models;

use Illuminate\Database\Eloquent\Model;
use Juzaweb\Cms\Core\Traits\UseSlug;
use Juzaweb\Cms\Core\Traits\UseThumbnail;

class Taxonomy extends Model
{
    use UseSlug, UseThumbnail;

    protected $table = 'taxonomies';
    protected $slugSource = 'name';
    protected $fillable = [
        'name',
        'description',
        'thumbnail',
        'slug',
        'posy_type',
        'taxonomy',
        'post_type',
        'parent_id',
        'total_post'
    ];

    public function parent()
    {
        return $this->belongsTo(Taxonomy::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Taxonomy::class, 'parent_id', 'id');
    }
}
