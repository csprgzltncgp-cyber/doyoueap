@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/form.css')}}?v={{time()}}">
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('content')
    {{ Breadcrumbs::render('admins.create') }}
    <h1> {{__('common.create-admin')}}</h1>
    <form method="post">
        {{csrf_field()}}
        <div class="form-group">
            <label for=""> {{__('common.admin-name')}}</label>
            <input type="text" name="name" value="" placeholder="{{__('common.admin-name')}}" required
                   autocomplete="off">
        </div>
        <div class="form-group">
            <label for="">{{__('common.admin-email')}}</label>
            <input type="email" name="email" value="" placeholder="{{__('common.admin-email')}}" required
                   autocomplete="off">
        </div>
        <div class="form-group">
            <label for="">{{__('common.admin-username')}}</label>
            <input type="text" name="username" value="" placeholder="{{__('common.admin-username')}}" required
                   autocomplete="off">
        </div>
        <div class="form-group">
            <label for="">{{__('common.admin-password')}}</label>
            <input type="password" name="password" value="" placeholder="{{__('common.admin-password')}}" required
                   autocomplete="off">
        </div>
        <div class="form-group">
            <label for="">{{__('common.admin-password-confirmation')}}</label>
            <input type="password" name="password_confirmation" value=""
                   placeholder="{{__('common.admin-password-confirmation')}}" required autocomplete="off">
        </div>
        <div class="form-group">
            <label for="">{{__('common.admin-authorization-level')}}</label>
            <select name="type" required>
                <option value="admin" selected>Admin</option>
                <option value="production_admin">Production Admin</option>
                <option value="production_translating_admin">Production Translating Admin</option>
                <option value="account_admin">Account Admin</option>
                <option value="financial_admin">Financial Admin</option>
                <option value="eap_admin">Eap Admin</option>
                <option value="todo_admin">TODO Admin</option>
                <option value="affiliate_search_admin">Affiliate Search Admin</option>
                <option value="supervisor_admin">Supervisor Admin</option>
            </select>
        </div>
        <div class="form-group">
            <label for="">{{__('common.country')}}</label>
            <select name="country_id" required>
                @foreach($countries as $country)
                    <option value="{{$country->id}}">{{$country->code}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="">{{__('crisis.language')}}</label>
            <select name="language_id" required>
                @foreach($languages as $language)
                    <option value="{{$language->id}}">{{$language->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="">{{__('common.operator-connected-account')}}</label>
            <select name="connected_account">
                <option value={{null}}>{{__('common.please-choose')}}</option>
                @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->username}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="button btn-radius d-flex justify-content-center align-items-center" name="button"
            style="--btn-max-width: var(--btn-min-width); --btn-margin-right: 0px;">
                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                <span>{{__('common.create')}}</span>
            </button>
        </div>
    </form>
@endsection
