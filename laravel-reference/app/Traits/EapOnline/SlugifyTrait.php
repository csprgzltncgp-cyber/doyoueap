<?php

namespace App\Traits\EapOnline;

use App\Models\EapOnline\EapArticle;
use App\Models\EapOnline\EapPodcast;
use App\Models\EapOnline\EapQuiz;
use App\Models\EapOnline\EapVideo;
use Illuminate\Support\Str;

trait SlugifyTrait
{
    public function slugify($text, $type)
    {
        $slug = Str::slug($text);
        $count = null;

        switch ($type) {
            case 'article':
                $count = EapArticle::query()->whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
                break;
            case 'video':
                $count = EapVideo::query()->whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
                break;
            case 'podcast':
                $count = EapPodcast::query()->whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
                break;
            case 'quiz':
                $count = EapQuiz::query()->whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
                break;
        }

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
