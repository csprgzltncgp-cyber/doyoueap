@extends('layout.master')

@section('title', 'Admin Dashboard')

@section('extra_js')
    <script src="/assets/js/chosen.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $('#countries').chosen();
        $('#cities').chosen();
        $('#permissions').chosen();
        $('#specializations').chosen();
        $('#languageSkills').chosen();
    </script>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bordered-checkbox.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cases/datetime.css') }}">
    <link href="{{ asset('assets/css/chosen.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/assets/css/form.css?v={{ time() }}">
    <style>
        .chosen-disabled {
            opacity: 1 !important;
        }

        #permissions_chosen,
        #cities_chosen {
            flex: 1 !important;
        }

        #permissions_chosen,
        #cities_chosen,
        #specializations_chosen,
        #languageSkills_chosen {
            flex: 1 !important;
        }

        #cities_chosen>ul>li.search-field {
            width: 120px !important;
        }
    </style>
@endsection

@section('content')
    <div>
        <h1>{{ $user->name }}</h1>

        <form style="max-width: 750px !important;">
            <div style="margin-bottom:70px">
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('eap-online.footer.menu_points.name') }}:
                        </div>
                    </div>
                    <input type="text" value="{{ $user->name }}" required disabled>
                </div>
            </div>

            <div style="margin-bottom:50px">
                <h1 style="font-size: 20px;">{{ __('expert-data.contact-informations') }}:</h1>
                <div class="input-group col-12 p-0">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            {{ __('common.email') }}:
                        </div>
                    </div>
                    <input type="email" value="{{ $user->email }}" required disabled>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4 mb-0">
                        <input type="text" class="col-12" placeholder="{{ __('expert-data.phone') }}" disabled>
                    </div>

                    <div class="form-group col-md-4 mb-0">
                        <div class="d-flex flex-column">
                            <div class="input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{ __('expert-data.phone_prefix') }}:
                                    </div>
                                </div>
                                <input type="text" value="{{ optional($user->expert_data)->phone_prefix }}"
                                    class="col-12" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-4 mb-0">
                        <input type="number" value="{{ optional($user->expert_data)->phone_number }}" class="col-12"
                            disabled>
                    </div>
                </div>
            </div>

            <div style="margin-bottom:70px">
                <h1 style="font-size: 20px;">{{ __('expert-data.professional-informations') }}:</h1>

                <div class="form-group">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('common.country') }}:
                            </div>
                        </div>
                        <select id="countries" multiple class="chosen-select" disabled>
                            @foreach ($countries as $country)
                                <option @if (in_array($country->id, $user->expertCountries->pluck('id')->toArray())) selected @endif value='{{ $country->id }}'>
                                    {{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-5 mb-0">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{ __('crisis.city') }}:
                                </div>
                            </div>
                            <select id="cities" multiple class="chosen-select" disabled>
                                @foreach ($cities as $city)
                                    <option @if (in_array($city->id, $user->cities->pluck('id')->toArray())) selected @endif value='{{ $city->id }}'>
                                        {{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('common.areas-of-experties') }}:
                            </div>
                        </div>
                        <select id="permissions" multiple class="chosen-select" disabled>
                            @foreach ($permissions as $permission)
                                <option @if (in_array($permission->id, $user->permission->pluck('id')->toArray())) selected @endif value='{{ $permission->id }}'>
                                    {{ $permission->translation->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if ($user->hasPermission(1))
                    <div class="form-group">
                        <div class="input-group col-12 p-0">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{ __('expert-data.specialization') }}:
                                </div>
                            </div>
                            <select id="specializations" multiple class="chosen-select" disabled>
                                @foreach ($specializations as $specialization)
                                    <option @if (in_array($specialization->id, $user->specializations->pluck('id')->toArray())) selected @endif
                                        value="{{ $specialization->id }}">
                                        {{ optional($specialization->translation)->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{ __('expert-data.language_skills') }}:
                            </div>
                        </div>
                        <select id="languageSkills" multiple class="chosen-select" disabled>
                            @foreach ($languageSkills as $languageSkill)
                                <option @if (in_array($languageSkill->id, $user->language_skills->pluck('id')->toArray())) selected @endif
                                    value="{{ $languageSkill->id }}">
                                    {{ $languageSkill->translation->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- <div class="w-full mt-5 d-flex">
            <button type="submit" style="width:auto;" class="button ml-0">
                {{__('common.save')}}
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 mb-1" style="width: 20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
            </button>
        </div> --}}
        </form>
    </div>
@endsection
