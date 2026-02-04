<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CaseInputValue;
use App\Models\Country;
use App\Models\Permission;
use App\Models\Riport;
use App\Models\RiportValue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HealthMapController extends Controller
{
    public function show(?Country $country = null)
    {
        $user = Auth::user();
        $company = $user->companies()->first();
        $current_country = $country ?? $user->country;
        $connected_companies = $company->get_connected_companies();

        $riport = Riport::query()
            ->where([
                'company_id' => $company->id,
                'is_active' => true,
            ])->with('values')->latest()->first();

        if (! $riport) {
            return view('client.health-map', ['connected_companies' => $connected_companies, 'current_company' => $company]);
        }

        $circles = $this->generate_gender_circles($current_country, $riport);

        if ($company->id == 717) {
            $countries = $company->countries->map(function ($country, string $index) {
                if (! in_array($country->id, [2, 6, 12])) {
                    $country->name = __('common.country').' '.$index;
                }

                return $country;
            });
        } else {
            $countries = $company->countries;
        }

        $health_map_data = [
            'countries' => $countries,
            'current_country' => $current_country,
            'circles' => $circles,
            'quarter' => get_last_quarter(),
        ];

        return view('client.health-map', ['health_map_data' => $health_map_data, 'connected_companies' => $connected_companies, 'current_company' => $company]);
    }

    private function generate_gender_circles(Country $country, Riport $riport)
    {
        $health_map_values = $this->generate_health_map_values($country, $riport);

        $circles = collect([]);
        $circle_sizes = ['large', 'medium', 'small'];

        foreach ($health_map_values as $gender) {
            $problem_types = $gender->get('problem_types');
            $circles->put($gender['id'], collect([]));

            if ($problem_types->count() < 1) {
                continue;
            }
            $sorted_problem_types = $problem_types->sortByDesc(fn ($value) => $value['count']);
            if ($problem_types->count() == 1) {
                $reduced_sizes = [$circle_sizes[0]];
            } elseif ($problem_types->count() == 2) {
                $reduced_sizes = [$circle_sizes[0], $circle_sizes[1]];
            } else {
                $reduced_sizes = $circle_sizes;
            }
            foreach ($reduced_sizes as $size_index => $size) {
                $problem_type = $sorted_problem_types->values()->get($size_index) ?: $sorted_problem_types->last();
                $most_used_age = $problem_type->get('ages')->sortByDesc(fn ($value) => $value['count'])->first();

                $circles[$gender['id']]->push([
                    'size' => $size,
                    'age_id' => $most_used_age['id'],
                    'problem_type_id' => $problem_type['id'],
                ]);
            }
        }

        return $circles;
    }

    private function generate_health_map_values(Country $country, Riport $riport)
    {
        $values_to_structure = $riport->values;
        $additional_riports = Riport::query()
            ->where([
                'company_id' => $riport->company->id,
                'is_active' => true,
            ])
            ->where('id', '!=', $riport->id)
            ->whereDate('from', '>=', Carbon::now()->quarter == 1 ? Carbon::parse($riport->from)->subYear()->startOfYear() : Carbon::parse($riport->from)->startOfYear())
            ->whereDate('to', '<=', Carbon::parse($riport->to)->format('Y-m-d'))
            ->with('values')->get();

        foreach ($additional_riports as $additional_riport) {
            foreach ($additional_riport->values as $value) {
                $values_to_structure->push($value);
            }
        }

        $structured_data = $this->structure_values($values_to_structure, $country);
        $heath_map_values = collect([]);

        if (! $structured_data->has('gender')) {
            return $heath_map_values;
        }

        $gender_index = 0;

        foreach ($structured_data['gender'] as $gender_transition => $gender_values) {
            $heath_map_values->push(collect([
                'id' => $gender_values['id'],
                'transition' => $gender_transition,
            ]));

            $problem_type_index = 0;

            $heath_map_values[$gender_index]['problem_types'] = collect([]);
            foreach ($structured_data['problem_type'] as $problem_type_transition => $problem_type_values) {
                $problem_type_count = $values_to_structure
                    ->where('country_id', $country->id)
                    ->filter(function ($item) use ($problem_type_values, $gender_values): bool {
                        $problem_type = $item->connected_values->filter(fn ($value): bool => $value->type == RiportValue::TYPE_PROBLEM_TYPE && $value->value == $problem_type_values['id'])->count();

                        $gender_type = $item->connected_values->filter(fn ($value): bool => $value->type == RiportValue::TYPE_GENDER && $value->value == $gender_values['id'])->count();

                        return $problem_type && $gender_type;
                    })
                    ->groupBy('connection_id')
                    ->count();

                if ($problem_type_count > 0) {
                    $heath_map_values[$gender_index]['problem_types']->push(collect([
                        'id' => $problem_type_values['id'],
                        'transition' => $problem_type_transition,
                        'count' => $problem_type_count,
                    ]));

                    $heath_map_values[$gender_index]['problem_types'][$problem_type_index]['ages'] = collect([]);
                    foreach ($structured_data['age'] as $age_transition => $age_values) {
                        $age_count = $values_to_structure
                            ->where('country_id', $country->id)
                            ->filter(function ($item) use ($problem_type_values, $gender_values, $age_values): bool {
                                $problem_type = $item->connected_values->filter(fn ($value): bool => $value->type == RiportValue::TYPE_PROBLEM_TYPE && $value->value == $problem_type_values['id'])->count();

                                $gender_type = $item->connected_values->filter(fn ($value): bool => $value->type == RiportValue::TYPE_GENDER && $value->value == $gender_values['id'])->count();

                                $age_type = $item->connected_values->filter(fn ($value): bool => $value->type == RiportValue::TYPE_AGE && $value->value == $age_values['id'])->count();

                                return $problem_type && $gender_type && $age_type;
                            })
                            ->groupBy('connection_id')
                            ->count();

                        if ($age_count > 0) {
                            $heath_map_values[$gender_index]['problem_types'][$problem_type_index]['ages']->push(collect([
                                'id' => $age_values['id'],
                                'transition' => $age_transition,
                                'count' => $age_count,
                            ]));
                        }
                    }

                    $problem_type_index++;
                }
            }

            $gender_index++;
        }

        return $heath_map_values;
    }

    private function structure_values($values, Country $country)
    {
        // Problem type
        $problem_type_values = $values
            ->where('country_id', $country->id)
            ->where('type', RiportValue::TYPE_PROBLEM_TYPE);

        $problem_type_total_count = $problem_type_values->count();
        $structured_values['problem_type'] = [];

        foreach ($problem_type_values->groupBy('value') as $permission_id => $permission_values) {
            $permission = Permission::query()->where('id', $permission_id)->first()->translation->value;
            $structured_values['problem_type'][$permission] = [
                'total_count' => $problem_type_total_count,
                'count' => $permission_values->count(),
                'id' => $permission_id,
            ];
        }

        // gender
        $gender_type_values = $values
            ->where('country_id', $country->id)
            ->where('type', RiportValue::TYPE_GENDER);

        $total_count = $gender_type_values->count();
        $structured_values['gender'] = [];

        foreach ($gender_type_values->sortBy('value')->groupBy('value') as $gender_id => $gender_values) {
            $case_input_value = CaseInputValue::withTrashed()->where('id', $gender_id)->first()->translation->value;
            $structured_values['gender'][$case_input_value] = [
                'total_count' => $total_count,
                'count' => $gender_values->count(),
                'id' => $gender_id,
            ];
        }

        // age
        $age_type_values = $values
            ->where('country_id', $country->id)
            ->where('type', RiportValue::TYPE_AGE);

        $total_count = $age_type_values->count();
        $structured_values['age'] = [];

        foreach ($age_type_values->sortBy('value')->groupBy('value') as $age_id => $age_values) {
            $case_input_value = CaseInputValue::withTrashed()->where('id', $age_id)->first()->translation->value;
            $structured_values['age'][$case_input_value] = [
                'total_count' => $total_count,
                'count' => $age_values->count(),
                'id' => $age_id,
            ];
        }

        return collect($structured_values);
    }
}
