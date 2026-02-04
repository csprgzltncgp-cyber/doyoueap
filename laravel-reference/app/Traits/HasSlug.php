<?php

namespace App\Traits;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @mixin TModel
 *
 * @property string $slugFrom
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function (self $model): void {
            if (! empty($model->getAttribute('slug'))) {
                return;
            }
            if (! method_exists($model, 'create_slug')) {
                return;
            }
            $model->setAttribute('slug', $model->create_slug($model->{$model->slugFrom}));
        });

        static::updating(function (self $model): void {
            if (method_exists($model, 'create_slug')) {
                $model->setAttribute('slug', $model->create_slug($model->{$model->slugFrom}));
            }
        });
    }

    private function create_slug($name)
    {
        $slug = strtolower((string) $name);
        $slug = str_replace(['[\', \']'], '', $slug);
        $slug = preg_replace('/\[.*\]/U', '', $slug);
        $slug = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $slug);
        $slug = htmlentities($slug, ENT_COMPAT, 'utf-8');
        $slug = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $slug);
        $slug = preg_replace(['/[^a-z0-9]/i', '/[-]+/'], '-', $slug);

        // slug repeat check
        $latest = $this->whereRaw("slug REGEXP '^{$slug}(-[0-9]+)?$'")
            ->latest('id')
            ->value('slug');

        if ($latest) {
            $pieces = explode('-', (string) $latest);
            $number = (int) end($pieces);
            $slug .= '-'.($number + 1);
        }

        return $slug;
    }
}
