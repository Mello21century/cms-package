<?php
/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzawebcms/juzawebcms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://github.com/juzawebcms/juzawebcms
 * @license    MIT
 *
 * Created by JUZAWEB.
 * Date: 6/8/2021
 * Time: 8:08 PM
 */

namespace Juzaweb\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\Facades\HookAction;
use Juzaweb\Models\Comment;
use Juzaweb\Models\Taxonomy;
use Illuminate\Support\Str;

/**
 * @method Builder wherePublish()
 * @method Builder whereTaxonomy($taxonomy)
 * @method Builder whereTaxonomyIn($taxonomies)
 */
trait PostTypeModel
{
    use ResourceModel, UseSlug, UseThumbnail, UseChangeBy;

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFilter($builder, $params = [])
    {
        if (empty($this->searchFields)) {
            $this->searchFields = ['title'];
        }

        if ($keyword = Arr::get($params, 'keyword')) {
            $builder->where(function (Builder $q) use ($keyword)  {
                foreach ($this->searchFields as $key => $attribute) {
                    $q->orWhere($attribute, 'like', '%'. $keyword .'%');
                }
            });
        }

        if ($status = Arr::get($params, 'status')) {
            $builder->where('status', '=', $status);
        }

        $taxonomies = HookAction::getTaxonomies($this->getPostType('key'));
        foreach ($taxonomies as $key => $taxonomy) {
            if ($ids = Arr::get($params, $key)) {
                if (!is_array($ids)) {
                    $ids = [$ids];
                }

                $builder->whereTaxonomyIn($ids);
            }
        }

        return $builder;
    }

    public function getStatuses()
    {
        return apply_filters(
            app(static::class)->getPostType('key') . '.statuses',
            [
                'publish' => trans('juzaweb::app.publish'),
                'private' => trans('juzaweb::app.private'),
                'draft' => trans('juzaweb::app.draft'),
                'trash' => trans('juzaweb::app.trash'),
            ]
        );
    }

    public function taxonomies()
    {
        return $this->belongsToMany(Taxonomy::class, 'term_taxonomies', 'term_id', 'taxonomy_id')
            ->withPivot(['term_type'])
            ->wherePivot('term_type', '=', $this->getPostType('key'));
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'object_id', 'id')->where('object_type', '=', $this->getPostType('key'));
    }

    public function getTaxonomies($taxonomy = null)
    {
        if (empty($taxonomy)) {
            return $this->taxonomies;
        }

        return $this->taxonomies->where('taxonomy', $taxonomy);
    }

    public function syncTaxonomies(array $attributes)
    {
        $postType = $this->getPostType('key');
        $taxonomies = HookAction::getTaxonomies($postType);
        foreach ($taxonomies as $taxonomy) {
            $this->syncTaxonomy($taxonomy->get('taxonomy'), $attributes, $postType);
        }
    }

    public function syncTaxonomy(string $taxonomy, array $attributes, string $postType = null)
    {
        $postType = $postType ?? $this->getPostType('key');
        $data = Arr::get($attributes, $taxonomy, []);
        $detachIds = $this->taxonomies()
            ->where('taxonomy', '=', $taxonomy)
            ->whereNotIn('id', $data)
            ->pluck('id')
            ->toArray();

        $this->taxonomies()->detach($detachIds);
        $this->taxonomies()
            ->syncWithoutDetaching(combine_pivot($data, [
                'term_type' => $postType
            ]), ['term_type' => $postType]);

        $taxonomies = Taxonomy::where('taxonomy', '=', $taxonomy)
            ->whereIn('id', array_merge($detachIds, $data))
            ->get();

        foreach ($taxonomies as $taxonomy) {
            $taxonomy->update([
                'total_post' => $taxonomy->posts()->count()
            ]);
        }
    }

    public function getPostType($key = null)
    {
        $modelClass = str_replace('\\', '_', static::class);
        $postType = HookAction::getPostTypes()
            ->where('model_key', '=', $modelClass)
            ->first();

        if (empty($key)) {
            return $postType;
        }

        return $postType->get($key);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     **/
    public function scopeWherePublish($builder)
    {
        $builder->where('status', '=', 'publish');
        return $builder;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $taxonomy
     *
     * @return \Illuminate\Database\Eloquent\Builder
     **/
    public function scopeWhereTaxonomy($builder, $taxonomy)
    {
        $builder->whereHas('taxonomies', function (Builder $q) use ($taxonomy) {
            $q->where($q->getModel()->getTable() . '.id', $taxonomy);
        });
        return $builder;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array $taxonomies
     *
     * @return \Illuminate\Database\Eloquent\Builder
     **/
    public function scopeWhereTaxonomyIn($builder, $taxonomies)
    {
        $builder->whereHas('taxonomies', function (Builder $q) use ($taxonomies) {
            $q->whereIn($q->getModel()->getTable() . '.id', $taxonomies);
        });
        return $builder;
    }

    public function getPermalink($key = null)
    {
        $permalink = HookAction::getPermalinks($this->getPostType('key'));

        if (empty($permalink)) {
            return false;
        }

        if (empty($key)) {
            return $permalink;
        }

        return $permalink->get($key);
    }

    public function getTitle($words = null)
    {
        if ($words > 0) {
            return apply_filters($this->getPostType('key') . '.get_title', Str::words($this->{$this->getFieldName()}, $words), $words);
        }

        return apply_filters($this->getPostType('key') . '.get_title', $this->{$this->getFieldName()}, $words);
    }

    public function getDescription($words = 24)
    {
        return apply_filters($this->getPostType('key') . '.get_description', Str::words($this->description, $words), $words);
    }

    public function getContent()
    {
        $type = $this->getPostType('key');

        return apply_filters($type . '.get_content', $this->content);
    }

    public function getLink()
    {
        if ($this->getTable() == 'pages') {
            return url()->to($this->slug) . '/';
        }

        $permalink = $this->getPermalink('base');
        if (empty($permalink)) {
            return false;
        }

        return url()->to($permalink . '/' . $this->slug) . '/';
    }
}
