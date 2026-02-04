@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('content')
    {{ Breadcrumbs::render('cities.create') }}
    <h1>{{__('common.create-city')}}</h1>
    <form method="post" class="row">
        {{csrf_field()}}
        <div class="col-12 col-sm-12s">
            <div class="form-group">
                <label for="">{{__('eap-online.footer.menu_points.name')}}</label>
                <input type="text" name="name" value="" placeholder="{{__('eap-online.footer.menu_points.name')}}"
                       required>
            </div>
            <div class="form-group">
                <label for="">{{__('common.country')}}</label>
                <select name="country_id" required>
                    @foreach($countries as $country)
                        <option value="{{$country->id}}">{{$country->code}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <button type="submit" class="button btn-radius d-flex justify-content-center align-items-center"
                style="--btn-max-width: var(--btn-min-width); --btn-margin-right: 0px;" name="button">
                    <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                    {{__('common.save')}}
                </button>
            </div>
        </div>
    </form>
@endsection
