<?php

namespace App\Traits\EapOnline;

use App\Models\EapOnline\EapCategory;
use App\Models\EapOnline\EapVisibility;
use Exception;

trait VisibilityTrait
{
    public function setVisibility(array $inputs, $resource_id, $from_date, $to_date, $type): void
    {
        $inputs['from_date'] = $from_date;
        $inputs['to_date'] = $to_date;
        $inputs['type'] = $type;
        $inputs['resource_id'] = $resource_id;
        EapVisibility::query()->create($inputs);
    }

    public function updateVisibility(array $inputs, $resource_id, $from_date, $to_date, $type): void
    {
        $inputs['from_date'] = $from_date;
        $inputs['to_date'] = $to_date;
        EapVisibility::query()->where(['type' => $type, 'resource_id' => $resource_id])->update($inputs);

        if (! $inputs['theme_of_the_month']) {
            EapVisibility::query()->where(['type' => $type, 'resource_id' => $resource_id])->update([
                'from_date' => null,
                'to_date' => null,
            ]);
        }
    }

    public function getVisibilities(): string
    {
        if (! $this->getAttribute('eap_visibility')) {
            return '<span style="color: rgb(219, 11, 32) !important;">'.__('eap-online.articles.wrong_article').'</span>';
        }

        try {
            $visibilities = [];

            if ($this->eap_visibility->self_care) {
                $visibilities[] = __('eap-online.articles.self_care');
            }

            if ($this->eap_visibility->after_assessment) {
                $visibilities[] = __('eap-online.articles.after_assessment');
            }

            if ($this->eap_visibility->theme_of_the_month) {
                $visibilities[] = __('eap-online.articles.theme_of_the_month');
            }

            if ($this->eap_visibility->home_page) {
                $visibilities[] = __('eap-online.articles.home_page');
            }

            if ($this->eap_visibility->burnout_page) {
                $visibilities[] = __('eap-online.articles.burnout_page');
            }

            if ($this->eap_visibility->domestic_violence_page) {
                $visibilities[] = __('eap-online.articles.domestic_violence_page');
            }

            return implode(', ', $visibilities);
        } catch (Exception) {
            return '<span style="color: rgb(219, 11, 32) !important;">'.__('eap-online.articles.wrong_article').'</span>';
        }
    }

    public function createVisibilityFormat($categories, $visibility): array
    {
        $self_help_categories = EapCategory::query()->where('type', 'self-help')->pluck('id')->toArray();
        $assessment_categories = EapCategory::query()->where('type', 'eap-assessment')->pluck('id')->toArray();
        $well_being_categories = EapCategory::query()->where('type', 'well-being')->pluck('id')->toArray();

        return [
            'self_care' => (array_intersect($self_help_categories, $categories ?? []) !== []),
            'after_assessment' => (array_intersect($assessment_categories, $categories ?? []) !== []),
            'theme_of_the_month' => ($visibility === 'theme_of_the_month'),
            'home_page' => ($visibility === 'home_page'),
            'well_being' => (array_intersect($well_being_categories, $categories ?? []) !== []),
            'burnout_page' => ($visibility === 'burnout_page'),
            'domestic_violence_page' => ($visibility === 'domestic_violence_page'),
        ];
    }
}
