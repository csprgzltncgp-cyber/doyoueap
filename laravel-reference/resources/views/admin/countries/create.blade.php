@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/form.css')}}?v={{time()}}">
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('content')
    {{ Breadcrumbs::render('countries.create') }}
    <h1>{{__('common.create-country')}}</h1>
    <form method="post" class="row">
        {{csrf_field()}}
        <div class="col-12 col-sm-12s">
            <div class="form-group">
                <label for="name">{{__('eap-online.footer.menu_points.name')}}</label>
                <input type="text" name="name" id="name" value=""
                       placeholder="{{__('eap-online.footer.menu_points.name')}}"
                       required>
            </div>
            <div class="form-group">
                <label for="code">{{__('eap-online.languages.country_code')}}</label>
                <input type="text" name="code" id="code" placeholder="{{__('eap-online.languages.country_code')}}" required>
            </div>
            <div class="form-group">
                <label for="email">{{__('common.company_main_operator_email')}}</label>
                <input type="email" name="email" id="email" placeholder="{{__('eap-online.users.email')}}" required>
            </div>

            <div class="form-group">
                <label for="timezone">{{__('common.timezone')}}</label>
                <select name="timezone" id="timezone" required>
                    @foreach(DateTimeZone::listIdentifiers(DateTimeZone::EUROPE) as $timezone => $name)
                        <option value="{{$name}}">{{$name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <button type="submit" style="--btn-max-width: var(--btn-min-width); --btn-margin-right: 0px" class="button btn-radius d-flex align-items-center">
                    <img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                    <span>
                        {{__('common.save')}}
                    </span>
                </button>
            </div>
        </div>
    </form>
@endsection
