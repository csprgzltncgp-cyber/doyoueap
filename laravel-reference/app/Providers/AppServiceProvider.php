<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\EapOnline\Assessment\EapType;
use App\Models\EapOnline\EapAnswer;
use App\Models\EapOnline\EapArticle;
use App\Models\EapOnline\EapCategory;
use App\Models\EapOnline\EapFooterMenu;
use App\Models\EapOnline\EapPrefix;
use App\Models\EapOnline\EapQuestion;
use App\Models\EapOnline\EapQuiz;
use App\Models\EapOnline\EapResult;
use App\Models\EapOnline\EapSection;
use App\Models\EapOnline\EapSetting;
use App\Models\EapOnline\EapVideo;
use App\Models\EapOnline\EapWebinar;
use App\Services\SzamlazzHu\CgpInvoicingClient;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use zoparga\SzamlazzHu\Client\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Paginator::useBootstrap();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        view()->composer('*', function ($view): void {
            $menu = Document::index();

            // ...with this variable
            $view->with('menu', $menu);
        });

        Relation::morphMap([
            //            EAP ONLINE
            'App\Models\Section' => EapSection::class,
            'App\Models\Result' => EapResult::class,
            'App\Models\Article' => EapArticle::class,
            'App\Models\Quiz' => EapQuiz::class,
            'App\Models\Video' => EapVideo::class,
            'App\Models\Webinar' => EapWebinar::class,
            'App\Models\Question' => EapQuestion::class,
            'App\Models\Prefix' => EapPrefix::class,
            'App\Models\Category' => EapCategory::class,
            'App\Models\Answer' => EapAnswer::class,
            'App\Models\Setting' => EapSetting::class,
            'App\Models\Assessment\Question' => \App\Models\EapOnline\Assessment\EapQuestion::class,
            'App\Models\Assessment\Answer' => \App\Models\EapOnline\Assessment\EapAnswer::class,
            'App\Models\Assessment\Result' => \App\Models\EapOnline\Assessment\EapResult::class,
            'App\Models\Assessment\Type' => EapType::class,
            'App\Models\WellBeing\Question' => \App\Models\EapOnline\WellBeing\EapQuestion::class,
            'App\Models\WellBeing\Answer' => \App\Models\EapOnline\WellBeing\EapAnswer::class,
            'App\Models\WellBeing\Result' => \App\Models\EapOnline\WellBeing\EapResult::class,
            'App\Models\WellBeing\Type' => \App\Models\EapOnline\WellBeing\EapType::class,
            'App\Models\StaticContent' => 'App\Models\EapOnline\EapStaticContent',
            'App\Models\FooterMenu' => EapFooterMenu::class,
        ]);

        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page'): LengthAwarePaginator {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            /** @phpstan-ignore-next-line */
            $collection = $this instanceof Collection ? $this : Collection::make($this);

            return new LengthAwarePaginator(
                $collection->forPage($page, $perPage),
                $total ?: $collection->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

        $this->app->bind(Client::class, fn ($app): CgpInvoicingClient => new CgpInvoicingClient(
            $app['config']['szamlazz-hu']['client'],
            new \GuzzleHttp\Client,
            $app['config']['szamlazz-hu']['merchant']
        ));
        $this->app->alias(Client::class, 'szamlazz-hu.client');
    }
}
