@extends('layout.master')

@section('title')
Operator Dashboard
@endsection

@section('extra_css')
<link rel="stylesheet" href="/assets/css/filter.css?v={{time()}}">
<link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
@endsection

@section('extra_js')
<script src="/assets/js/datetime.js" charset="utf-8"></script>
<script>
$(function(){
  $('.datepicker').datepicker({
    'format' : 'yyyy-mm-dd'
  });
  arrowClick();
});

function arrowClick(){
  $('.filter-button').click(function(){
    var options = $(this).closest('.filter').find('.options');
    options.toggleClass('d-none');
    if(options.hasClass('d-none')){
      $(this).find('i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
    }
    else{
      $(this).find('i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
    }
  });
}


</script>
@endsection

@section('content')
<div class="row mt-5">
  <div class="col-12">
    <h1>{{__('common.filter')}}</h1>
    <form method="post" class="row">
      {{csrf_field()}}
      <div class="filter-holder col-6">
        <div class="filter">
            <p>{{ __('common.case_id') }}</p>
            <button type="button" class="filter-button">
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </button>
            <div class="options d-none">
                <input type="text" name="attributes[case_identifier][]" placeholder="{{ __('common.case_id') }}">
            </div>
        </div>
      </div>
        <div class="filter-holder col-6">
            <div class="filter">
                <p>{{ __('common.status') }}</p>
                <button type="button" class="filter-button">
                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </button>
                <div class="options d-none">
                    <select name="attributes[status][]">
                        <option value="-1">{{ __('common.please-choose') }}</option>
                        <option value="opened">{{ __('common.open') }}</option>
                        <option value="assigned_to_expert">{{ __('common.assigned-to-an-expert') }}</option>
                        <option value="employee_contacted">{{ __('common.contacted') }}</option>
                        <option value="client_unreachable">{{ __('common.the-client-is-unreachable') }}</option>
                        <option value="confirmed">{{ __('common.approved') }}</option>
                        <option value="client_unreachable_confirmed">{{ __('common.the-client-is-unavailable-locked') }}</option>
                        <option value="interrupted">{{ __('common.counseling-was-interrupted') }}</option>
                        <option value="interrupted_confirmed">{{ __('common.counseling-was-interrupted-and-closed') }}</option>
                    </select>
                </div>
            </div>
        </div>
      @foreach($filters as $filter)
        <div class="filter-holder col-6">
          <div class="filter">
            <p>{{$filter->translation ? $filter->translation->value : null}}</p>
            <button type="button" class="filter-button"><svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg></button>
            <div class="options d-none">
                @if($filter->type == 'date')
                    <input type="text" name="filter[{{$filter->id}}][from][]" class="datepicker" placeholder="{{__('common.from')}}" style="width:38%;margin-right:4%;"/>
                    <input type="text" name="filter[{{$filter->id}}][to][]" class="datepicker" placeholder="{{__('common.to')}}" style="width:38%;"/>
                @elseif($filter->type == 'text')
                    <input type="text" name="filter[{{$filter->id}}][value][]" placeholder="{{$filter->translation ? $filter->translation->value : null }}">
                @elseif($filter->type == 'integer')
                    <input type="number" name="filter[{{$filter->id}}][value][]" placeholder="{{$filter->translation ? $filter->translation->value : null }}">
                @elseif($filter->type == 'double')
                    <input type="text" name="filter[{{$filter->id}}][value][]" placeholder="{{$filter->translation ? $filter->translation->value : null }}">
                @elseif($filter->default_type == 'company_chooser')
                <select name="attributes[company_id][]">
                    <option value="-1">{{__('common.please-choose-one')}}</option>
                    @foreach($companies as $company)
                    <option value="{{$company->id}}">{{$company->name}}</option>
                    @endforeach
                </select>
                @elseif($filter->default_type == 'case_type' )
                    <select name="filter[{{$filter->id}}][value][]">
                        <option value="-1">{{__('common.please-choose-one')}}</option>
                        @foreach($permissions as $permission)
                        <option value="{{$permission->id}}">{{$permission->translation->value}}</option>
                        @endforeach
                    </select>
                @elseif($filter->default_type == 'case_language_skill' )
                    <select name="filter[{{$filter->id}}][value][]">
                        <option value="-1">{{__('common.please-choose-one')}}</option>
                        @foreach($languageSkills as $language)
                        <option value="{{$language->id}}">{{$language->translation->value}}</option>
                        @endforeach
                    </select>
                @elseif($filter->default_type == 'case_specialization' )
                    <select name="filter[{{$filter->id}}][value][]">
                        <option value="-1">{{__('common.please-choose-one')}}</option>
                        @foreach($specializations as $specialization)
                        <option value="{{$specialization->id}}">{{$specialization->translation->value}}</option>
                        @endforeach
                    </select>
                @elseif($filter->type == 'select')
                <select name="filter[{{$filter->id}}][value][]">
                <option value="-1">{{__('common.please-choose-one')}}</option>
                @foreach($filter->values as $permission)
                    <option value="{{$permission->id}}">{{$permission->translation ? $permission->translation->value : null}}</option>
                @endforeach
                </select>
              @endif
            </div>
          </div>
        </div>
      @endforeach
      <div class="filter-holder col-6">
        <div class="filter">
            <p>{{__('common.countries')}}</p>
            <button type="button" class="filter-button">
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </button>
            <div class="options d-none">
                <select name="attributes[country_id][]">
                    <option value="-1">{{ __('common.please-choose') }}</option>
                    @foreach($countries as $country)
                        <option value="{{$country->id}}">{{$country->code}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="filter-holder col-6">
        <div class="filter">
            <p>{{__('common.experts')}}</p>
            <button type="button" class="filter-button">
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </button>
            <div class="options d-none">
                <select name="expert">
                    <option value="-1">{{ __('common.please-choose') }}</option>
                    @foreach($experts as $expert)
                        <option value="{{$expert->id}}">{{$expert->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="filter-holder col-6">
        <div class="filter">
            <p>Contract Holders</p>
            <button type="button" class="filter-button">
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </button>
            <div class="options d-none">
                <select name="contract_holder_id">
                    <option value="-1">{{ __('common.please-choose') }}</option>
                    @foreach($contractHolders as $contractHolder)
                        <option value="{{$contractHolder->id}}">{{$contractHolder->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="filter-holder col-6">
        <div class="filter">
            <p>OrgId</p>
            <button type="button" class="filter-button">
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </button>
            <div class="options d-none">
                <input type="text" name="org_id" placeholder="OrgId">
            </div>
        </div>
    </div>

    <div class="filter-holder col-6">
        <div class="filter">
            <p>{{__('common.consultation_date')}}</p>
            <button type="button" class="filter-button">
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </button>
            <div class="options d-none">
                <input type="text" name="consultation_date_from" class="datepicker"
                               placeholder="{{ __('common.from') }}" style="width:38%;margin-right:4%;"/>
                <input type="text" name="consultation_date_to" class="datepicker"
                        placeholder="{{ __('common.to') }}" style="width:38%;"/>
            </div>
        </div>
    </div>

      <div class="col-12 mt-5 mb-5">
        <button type="submit" class="button btn-radius"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
</svg> {{__('common.filter')}}</button>
      </div>
    </form>
  </div>
</div>
@endsection
