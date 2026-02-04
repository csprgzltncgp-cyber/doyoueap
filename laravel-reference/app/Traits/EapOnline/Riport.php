<?php

namespace App\Traits\EapOnline;

use App\Models\Country;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapRiport;
use App\Models\EapOnline\EapRiportValue;
use App\Models\EapOnline\EapUser;
use App\Models\EapOnline\Statistics\EapAssessment;
use App\Models\EapOnline\Statistics\EapCategory;
use App\Models\EapOnline\Statistics\EapLogin;
use App\Models\EapOnline\Statistics\EapSelfHelp;
use Carbon\Carbon;

trait Riport
{
    public function generate_eap_online_riport_values($company, $country, $riport, $totalView = false): array
    {
        $riport_values = [];

        // Registers
        $riport_values['new_registers'] = optional($riport->eap_riport_values()
            ->where(['statistics' => 'App\Models\EapOnline\Statistics\EapRegister'])
            ->when(! $totalView, fn ($query) => $query->where('country_id', $country->id))
            ->get());
        if ($totalView) {
            $riport_values['new_registers'] = $riport_values['new_registers']->sum('count');
        } else {
            $riport_values['new_registers'] = $riport_values['new_registers']->first()->current_count;
        }

        $riport_values['all_registers'] = EapUser::query()->where(['company_id' => $company->id])
            ->when(! $totalView, fn ($query) => $query->where('country_id', $country->id))
            ->whereDate('created_at', '<', Carbon::parse($riport->from)->startOfDay())->count() + $riport_values['new_registers'];

        // Logins
        $riport_values['logins'] = $riport->eap_riport_values()
            ->where(['statistics' => EapLogin::class])
            ->when(! $totalView, fn ($query) => $query->where('country_id', $country->id))
            ->get();
        if ($totalView) {
            $riport_values['logins'] = $riport_values['logins']->sum('count');
        } else {
            $riport_values['logins'] = $riport_values['logins']->first()->current_count;
        }

        // if the current quarter is not the first
        if (Carbon::parse($riport->from)->quarter > 1) {
            $riport_values['all_logins'] = EapRiport::query()
                ->where('company_id', $company->id)->get()
                ->whereBetween('from', [Carbon::parse($riport->from)->startOfYear(), Carbon::parse($riport->from)->endOfMonth()])
                ->map(function (EapRiport $riport) use ($country, $totalView) {

                    /** @var EapRiportValue|null $value */
                    $value = $riport->eap_riport_values()
                        ->where(['statistics' => EapLogin::class])
                        ->when(! $totalView, fn ($query) => $query->where('country_id', $country->id))
                        ->first();

                    if ($value !== null) {
                        return $value->current_count;
                    }

                })->sum();
        } else {
            $riport_values['all_logins'] = 0;
        }

        $eap_lang = EapLanguage::query()->where('code', app()->getLocale())->first();

        // Articles
        $article_values = $riport->eap_riport_values()->where(['statistics' => EapCategory::class, 'statistics_subtype' => EapCategory::TYPE_ARTICLE])
            ->when(! $totalView, fn ($query) => $query->where('country_id', $country->id))
            ->get();
        $articles_total_count = $article_values->sum('current_count');

        $riport_values['articles'] = [];
        $article_values->map(function ($value) use ($articles_total_count, &$riport_values, $eap_lang): void {
            $translation = \App\Models\EapOnline\EapCategory::query()->find($value->statistics_type)->get_translation(($eap_lang) ? $eap_lang->id : 1)->value;

            if (array_key_exists($translation, $riport_values['articles'])) {
                $riport_values['articles'][$translation]['count'] += $value->current_count;
            } else {
                $riport_values['articles'] = array_merge($riport_values['articles'], [$translation => [
                    'total_count' => $articles_total_count,
                    'count' => $value->current_count,
                ]]);
            }
        });

        // Videos
        $video_values = $riport->eap_riport_values()->where(['statistics' => EapCategory::class, 'statistics_subtype' => EapCategory::TYPE_VIDEO])
            ->when(! $totalView, fn ($query) => $query->where('country_id', $country->id))
            ->get();
        $video_total_count = $video_values->sum('current_count');
        $riport_values['videos'] = [];
        $video_values->map(function ($value) use ($video_total_count, &$riport_values, $eap_lang): void {
            $translation = \App\Models\EapOnline\EapCategory::query()->find($value->statistics_type)->get_translation(($eap_lang) ? $eap_lang->id : 1)->value;

            if (array_key_exists($translation, $riport_values['videos'])) {
                $riport_values['videos'][$translation]['count'] += $value->current_count;
            } else {
                $riport_values['videos'] = array_merge($riport_values['videos'], [$translation => [
                    'total_count' => $video_total_count,
                    'count' => $value->current_count,
                ]]);
            }
        });

        // Podcasts
        $podcast_values = $riport->eap_riport_values()->where(['statistics' => EapCategory::class, 'statistics_subtype' => EapCategory::TYPE_PODCAST])
            ->when(! $totalView, fn ($query) => $query->where('country_id', $country->id))
            ->get();
        $podcast_total_count = $podcast_values->sum('current_count');
        $riport_values['podcasts'] = [];
        $podcast_values->map(function ($value) use ($podcast_total_count, &$riport_values, $eap_lang): void {
            $translation = \App\Models\EapOnline\EapCategory::query()->find($value->statistics_type)->get_translation(($eap_lang) ? $eap_lang->id : 1)->value;

            if (array_key_exists($translation, $riport_values['podcasts'])) {
                $riport_values['podcasts'][$translation]['count'] += $value->current_count;
            } else {
                $riport_values['podcasts'] = array_merge($riport_values['podcasts'], [$translation => [
                    'total_count' => $podcast_total_count,
                    'count' => $value->current_count,
                ]]);
            }
        });

        // self help
        $self_help_values = $riport->eap_riport_values()->where(['statistics' => EapSelfHelp::class])
            ->when(! $totalView, fn ($query) => $query->where('country_id', $country->id))
            ->get();
        $self_help_total_count = $self_help_values->sum('current_count');
        $riport_values['self_help'] = [];
        $self_help_values->map(function ($value) use ($self_help_total_count, &$riport_values, $eap_lang): void {
            $translation = \App\Models\EapOnline\EapCategory::query()->find($value->statistics_type)->get_translation(($eap_lang) ? $eap_lang->id : 1)->value;

            if (array_key_exists($translation, $riport_values['self_help'])) {
                $riport_values['self_help'][$translation]['count'] += $value->current_count;
            } else {
                $riport_values['self_help'] = array_merge($riport_values['self_help'], [$translation => [
                    'total_count' => $self_help_total_count,
                    'count' => $value->current_count,
                ]]);
            }
        });

        // assessment
        $assessment_values = $riport->eap_riport_values()
            ->where(['statistics' => EapAssessment::class])
            ->when(! $totalView, fn ($query) => $query->where('country_id', $country->id))
            ->get();
        $assessment_total_count = $assessment_values->sum('current_count');
        $riport_values['assessment'] = [];
        $assessment_values->map(function ($value) use ($assessment_total_count, &$riport_values): void {
            if ($value->statistics_subtype === EapAssessment::TYPE_ASSESSMENT) {
                $translation = __('eap-online.menu-visibilities.assessment');
            } elseif ($value->statistics_subtype === EapAssessment::TYPE_MOOD_METER) {
                $translation = __('eap-online.riports.mood_meter');
            } elseif ($value->statistics_subtype === EapAssessment::TYPE_WELL_BEING) {
                $translation = __('eap-online.riports.well_being');
            } else {
                $translation = __('eap-online.riports.assessment');
            }

            if (array_key_exists($translation, $riport_values['assessment'])) {
                $riport_values['assessment'][$translation]['count'] += $value->current_count;
            } else {
                $riport_values['assessment'] = array_merge($riport_values['assessment'], [$translation => [
                    'total_count' => $assessment_total_count,
                    'count' => $value->current_count,
                ]]);
            }
        });

        return $riport_values;
    }

    public function get_eap_online_riport_intervals(): array
    {
        if (Carbon::now()->quarter == 1) {
            $from = Carbon::parse(Carbon::now()->subYear()->startOfYear()->format('Y-m-d'));
            $to = Carbon::parse(Carbon::now()->subYear()->startOfYear()->format('Y-m-d'))->addMonthsWithoutOverflow(2)->endOfMonth();
        } else {
            $from = Carbon::parse(Carbon::now()->startOfYear()->format('Y-m-d'));
            $to = Carbon::parse(Carbon::now()->startOfYear()->format('Y-m-d'))->addMonthsWithoutOverflow(2)->endOfMonth();
        }
        $now = config('app.env') != 'production' ? now()->addMonth()->format('Y-m-d') : now()->format('Y-m-d');
        $intervals = [];
        $quarter = 1;

        while ($to < $now) {
            $intervals[$quarter] = [
                'from' => $from->copy()->format('Y-m-d'),
                'to' => $to->copy()->format('Y-m-d'),
            ];

            $from = $to->copy()->addDay();
            $to = $to->copy()->addDay()->addMonthsWithoutOverflow(2)->endOfMonth();
            $quarter++;
        }

        return $intervals;
    }

    public function generate_riport($company, $riport, $from, $to): void
    {
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();

        $company->load(
            [
                'eap_self_help_statistics' => fn ($q) => $q->whereBetween('self_help_statistics.created_at', [$from, $to]),
                'eap_category_statistics' => fn ($q) => $q->whereBetween('category_statistics.created_at', [$from, $to]),
                'eap_assessment_statistics' => fn ($q) => $q->whereBetween('assessment_statistics.created_at', [$from, $to]),
                'eap_login_statistics' => fn ($q) => $q->whereBetween('login_statistics.created_at', [$from, $to]),
            ]
        );

        // self help value
        foreach ($company->eap_self_help_statistics->groupBy('country_id') as $country_id => $country_group) {
            foreach ($country_group->groupBy('category_id') as $type => $category_group) {
                $riport->eap_riport_values()->create([
                    'statistics' => EapSelfHelp::class,
                    'statistics_type' => $type,
                    'count' => count($category_group),
                    'country_id' => $country_id,
                ]);
            }
        }

        // login value
        foreach ($company->eap_login_statistics->groupBy('country_id') as $country_id => $country_group) {
            $riport->eap_riport_values()->create([
                'statistics' => EapLogin::class,
                'count' => count($country_group),
                'country_id' => $country_id,
            ]);
        }

        // register Value
        foreach ($company->countries as $country) {
            $riport->eap_riport_values()->create([
                'statistics' => 'App\Models\EapOnline\Statistics\EapRegister',
                'count' => EapUser::query()->where(['company_id' => $company->id, 'country_id' => $country->id])->whereBetween('created_at', [Carbon::parse($riport->from)->startOfDay(), Carbon::parse($riport->to)->endOfDay()])->count(),
                'country_id' => $country->id,
            ]);
        }

        // category value
        foreach ($company->eap_category_statistics->groupBy('country_id') as $country_id => $country_group) {
            foreach ($country_group->groupBy('type') as $statistics_subtype => $types) {
                foreach ($types->groupBy('category_id') as $statistics_type => $values) {
                    $riport->eap_riport_values()->create([
                        'statistics' => EapCategory::class,
                        'statistics_type' => $statistics_type,
                        'statistics_subtype' => $statistics_subtype,
                        'count' => count($values),
                        'country_id' => $country_id,
                    ]);
                }
            }
        }

        // assessment value
        foreach ($company->eap_assessment_statistics->groupBy('country_id') as $country_id => $country_group) {
            foreach ($country_group->groupBy('type') as $statistics_subtype => $values) {
                $riport->eap_riport_values()->create([
                    'statistics' => EapAssessment::class,
                    'statistics_subtype' => $statistics_subtype,
                    'count' => count($values),
                    'country_id' => $country_id,
                ]);
            }
        }

        $self_help_categories = \App\Models\EapOnline\EapCategory::query()->where(['type' => 'self-help', 'parent_id' => null])->get();
        $article_categories = \App\Models\EapOnline\EapCategory::query()->where(['type' => 'all-articles', 'parent_id' => null])->get();
        $video_categories = \App\Models\EapOnline\EapCategory::query()->where(['type' => 'all-videos', 'parent_id' => null])->get();
        $podcast_categories = \App\Models\EapOnline\EapCategory::query()->where(['type' => 'all-podcasts', 'parent_id' => null])->get();

        // missing values
        foreach ($company->countries as $country) {
            // self-help
            foreach ($self_help_categories as $self_help_category) {
                EapRiportValue::query()->firstOrCreate([
                    'statistics' => EapSelfHelp::class,
                    'statistics_type' => $self_help_category->id,
                    'country_id' => $country->id,
                    'riport_id' => $riport->id,
                ], [
                    'count' => 0,
                ]);
            }

            // login
            EapRiportValue::query()->firstOrCreate([
                'statistics' => EapLogin::class,
                'country_id' => $country->id,
                'riport_id' => $riport->id,
            ], [
                'count' => 0,
            ]);

            // category - article
            foreach ($article_categories as $article_category) {
                EapRiportValue::query()->firstOrCreate([
                    'statistics' => EapCategory::class,
                    'statistics_type' => $article_category->id,
                    'statistics_subtype' => EapCategory::TYPE_ARTICLE,
                    'country_id' => $country->id,
                    'riport_id' => $riport->id,
                ], [
                    'count' => 0,
                ]);
            }

            // category - video
            foreach ($video_categories as $video_category) {
                EapRiportValue::query()->firstOrCreate([
                    'statistics' => EapCategory::class,
                    'statistics_type' => $video_category->id,
                    'statistics_subtype' => EapCategory::TYPE_VIDEO,
                    'country_id' => $country->id,
                    'riport_id' => $riport->id,
                ], [
                    'count' => 0,
                ]);
            }

            // category - podcast
            foreach ($podcast_categories as $podcast_category) {
                EapRiportValue::query()->firstOrCreate([
                    'statistics' => EapCategory::class,
                    'statistics_type' => $podcast_category->id,
                    'statistics_subtype' => EapCategory::TYPE_PODCAST,
                    'country_id' => $country->id,
                    'riport_id' => $riport->id,
                ], [
                    'count' => 0,
                ]);
            }

            // assessment - assessment
            EapRiportValue::query()->firstOrCreate([
                'statistics' => EapAssessment::class,
                'statistics_subtype' => EapAssessment::TYPE_ASSESSMENT,
                'country_id' => $country->id,
                'riport_id' => $riport->id,
            ], [
                'count' => 0,
            ]);

            // assessment - well-being
            EapRiportValue::query()->firstOrCreate([
                'statistics' => EapAssessment::class,
                'statistics_subtype' => EapAssessment::TYPE_WELL_BEING,
                'country_id' => $country->id,
                'riport_id' => $riport->id,
            ], [
                'count' => 0,
            ]);

            // assessment - mood meter
            EapRiportValue::query()->firstOrCreate([
                'statistics' => EapAssessment::class,
                'statistics_subtype' => EapAssessment::TYPE_MOOD_METER,
                'country_id' => $country->id,
                'riport_id' => $riport->id,
            ], [
                'count' => 0,
            ]);
        }

        // delete statistics
        $company->eap_self_help_statistics()->whereBetween('self_help_statistics.created_at', [$from, $to])->delete();
        $company->eap_login_statistics()->whereBetween('login_statistics.created_at', [$from, $to])->delete();
        $company->eap_category_statistics()->whereBetween('category_statistics.created_at', [$from, $to])->delete();
        $company->eap_assessment_statistics()->whereBetween('assessment_statistics.created_at', [$from, $to])->delete();
    }

    public function get_eap_online_riport_data($from, $to, ?Country $country = null, $totalView = false): ?array
    {
        $user = auth()->user();
        $company = $user->companies()->first();

        if ($totalView) {
            $values = [];
            $companies = $company->get_connected_companies();
            $riport = [];
            foreach ($companies as $company) {
                $riports = EapRiport::query()
                    ->with('eap_riport_values')
                    ->where([
                        'is_active' => true,
                        'company_id' => $company->id,
                    ])->get();

                if ($riports->count() <= 0) {
                    return null;
                }

                $riport = $riports
                    ->where('from', Carbon::parse($from))
                    ->where('to', Carbon::parse($to))
                    ->first() ?? $riports->last();

                $values[] = $this->generate_eap_online_riport_values($company, $country, $riport, $totalView);
            }

            $values = $this->merge_and_sum_riport_values($values);
        } else {
            $riports = EapRiport::query()
                ->where([
                    'company_id' => $company->id,
                    'is_active' => true,
                ])->whereHas('eap_riport_values', fn ($query) => $query->where('country_id', $country->id))->with('eap_riport_values')->get();

            if ($riports->count() <= 0) {
                return null;
            }

            $riport = $riports
                ->where('from', Carbon::parse($from))
                ->where('to', Carbon::parse($to))
                ->first() ?? $riports->last();

            $values = $this->generate_eap_online_riport_values($company, $country, $riport);
        }

        return [
            'values' => $values,
            'from' => optional($riport)->from,
            'to' => optional($riport)->to,
            'quarter' => Carbon::parse($from)->quarter,
        ];
    }

    public function merge_and_sum_riport_values(array $arrays): array
    {
        $result = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $result[$key] = isset($result[$key]) && is_array($result[$key])
                        ? $this->merge_and_sum_riport_values([$result[$key], $value])
                        : $value;
                } else {
                    $result[$key] = isset($result[$key]) ? $result[$key] + $value : $value;
                }
            }
        }

        return $result;
    }
}
