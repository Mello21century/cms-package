<?php

namespace Juzaweb\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\Facades\HookAction;
use Juzaweb\Traits\ResourceModel;
use Juzaweb\Traits\UseSlug;
use Juzaweb\Traits\UseThumbnail;

/**
 * Juzaweb\Models\Taxonomy
 *
 * @property int $id
 * @property string $name
 * @property string|null $thumbnail
 * @property string|null $description
 * @property string $slug
 * @property string $post_type
 * @property string $taxonomy
 * @property int|null $parent_id
 * @property int $total_post
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Taxonomy[] $children
 * @property-read int|null $children_count
 * @property-read Taxonomy|null $parent
 * @method static Builder|Taxonomy newModelQuery()
 * @method static Builder|Taxonomy newQuery()
 * @method static Builder|Taxonomy query()
 * @method static Builder|Taxonomy whereCreatedAt($value)
 * @method static Builder|Taxonomy whereDescription($value)
 * @method static Builder|Taxonomy whereId($value)
 * @method static Builder|Taxonomy whereName($value)
 * @method static Builder|Taxonomy whereParentId($value)
 * @method static Builder|Taxonomy wherePostType($value)
 * @method static Builder|Taxonomy whereSlug($value)
 * @method static Builder|Taxonomy whereTaxonomy($value)
 * @method static Builder|Taxonomy whereThumbnail($value)
 * @method static Builder|Taxonomy whereTotalPost($value)
 * @method static Builder|Taxonomy whereUpdatedAt($value)
 * @method static Builder|Taxonomy whereFilter($params = [])
 * @mixin \Eloquent
 */
class Taxonomy extends Model
{
    use UseSlug, UseThumbnail, ResourceModel;

    protected $table = 'taxonomies';
    protected $slugSource = 'name';
    protected $fillable = [
        'name',
        'description',
        'thumbnail',
        'slug',
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

    public function posts($postType = null)
    {
        $postType = $postType ? $postType : $this->getPostType('key');
        $postModel = $this->getPostType('model');
        return $this->belongsToMany($postModel, 'term_taxonomies', 'taxonomy_id', 'term_id')
            ->withPivot(['term_type'])
            ->wherePivot('term_type', '=', $postType);
    }

    /**
     * @param Builder $builder
     * @param array $params
     *
     * @return Builder
     */
    public function scopeWhereFilter($builder, $params = [])
    {
        if ($taxonomy = Arr::get($params, 'taxonomy')) {
            $builder->where('taxonomy', '=', $taxonomy);
        }

        if ($postType = Arr::get($params, 'post_type')) {
            $builder->where('post_type', '=', $postType);
        }

        if ($keyword = Arr::get($params, 'keyword')) {
            $builder->where(function (Builder $q) use ($keyword) {
                $q->where('name', 'like', '%'. $keyword .'%');
                $q->orWhere('description', 'like', '%'. $keyword .'%');
            });
        }

        return $builder;
    }

    public function getPostType($key = null)
    {
        $postType = HookAction::getPostTypes($this->post_type);
        if ($key) {
            return $postType->get($key);
        }

        return $postType;
    }

    public function getPermalink($key = null)
    {
        $permalink = HookAction::getPermalinks($this->taxonomy);

        if (empty($permalink)) {
            return false;
        }

        if (empty($key)) {
            return $permalink;
        }

        return $permalink->get($key);
    }

    public function getLink()
    {
        $permalink = $this->getPermalink('base');
        if (empty($permalink)) {
            return false;
        }

        return url()->to($permalink . '/' . $this->slug) . '/';
    }

    public function getName()
    {
        return $this->name;
    }
}
