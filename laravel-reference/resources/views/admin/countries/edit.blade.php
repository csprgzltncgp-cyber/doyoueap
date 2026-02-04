@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/form.css')}}?v={{time()}}">
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('content')
    {{ Breadcrumbs::render('countries.edit', $country) }}
    <h1>{{$country->name}}</h1>
    @if($errors->any())
        <div class="alert alert-danger" role="alert" style="max-width: 320px;">
            {{$errors->first()}}
        </div>
    @endif
    <form method="post" class="row">
        {{csrf_field()}}
        @method('patch')
        <div class="col-12 col-sm-12s">
            <div class="form-group">
                <label for="name">{{__('eap-online.footer.menu_points.name')}}</label>
                <input type="text" name="name" id="name" value="{{$country->name}}"
                       placeholder="{{__('eap-online.footer.menu_points.name')}}"
                       required>
            </div>
            <div class="form-group">
                <label for="code">{{__('eap-online.languages.country_code')}}</label>
                <input type="text" name="code" id="code" value="{{$country->getRawOriginal('code')}}"
                       placeholder="{{__('eap-online.languages.country_code')}}" required>
            </div>
            <div class="form-group">
                <label for="email">{{__('common.company_main_operator_email')}}</label>
                <input type="email" name="email" id="email" value="{{$country->email}}"
                       placeholder="{{__('eap-online.users.email')}}" required>
            </div>

            <div class="form-group">
                <label for="timezone">{{__('common.timezone')}}</label>
                <select name="timezone" id="timezone" required>
                    @foreach(DateTimeZone::listIdentifiers(DateTimeZone::EUROPE) as $timezone => $name)
                        <option {{$name == $country->timezone ? 'selected' : ''}} value="{{$name}}">{{$name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <button class="button btn-radius d-flex justify-content-center align-items-center" style="--btn-max-width:var(--btn-min-width); --btn-margin-right:0px;" type="submit">
                    <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                    {{__('common.save')}}
                </button>
            </div>
        </div>
    </form>
@endsection
