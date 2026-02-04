@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/form.css')}}?v={{time()}}">
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('content')
    {{ Breadcrumbs::render('admins.edit', $user) }}
    <h1>{{$user->name}}</h1>
    <form method="post">
        {{csrf_field()}}
        <div class="form-group">
            <label for="">{{__('common.admin-name')}}</label>
            <input type="text" name="name" value="{{$user->name}}" placeholder="{{__('common.admin-name')}}" required>
        </div>
        <div class="form-group">
            <label for="">{{__('common.admin-email')}}</label>
            <input type="email" name="email" value="{{$user->email}}" placeholder="{{__('common.admin-email')}}" required>
        </div>
        <div class="form-group">
            <label for="">{{__('common.admin-username')}}</label>
            <input type="text" name="username" value="{{$user->username}}" placeholder="{{__('common.admin-username')}}" required>
        </div>
        <div class="form-group">
            <label for="">{{__('common.admin-authorization-level')}}</label>
            <select name="type" required>
                <option @if($user->type == 'admin') selected @endif  value="admin">Admin</option>
                <option @if($user->type == 'production_admin') selected @endif value="production_admin">Production Admin</option>
                <option @if($user->type == 'production_translating_admin') selected @endif value="production_translating_admin">Production Translating Admin</option>
                <option @if($user->type == 'account_admin') selected @endif value="account_admin">Account Admin</option>
                <option @if($user->type == 'financial_admin') selected @endif value="financial_admin">Financial Admin</option>
                <option @if($user->type == 'eap_admin') selected @endif value="eap_admin">Eap Admin</option>
                <option @if($user->type == 'todo_admin') selected @endif value="todo_admin">TODO Admin</option>
                <option @if($user->type == 'affiliate_search_admin') selected @endif value="affiliate_search_admin">Affiliate Search Admin</option>
                <option @if($user->type == 'supervisor_admin') selected @endif value="supervisor_admin">Supervisor Admin</option>
            </select>
        </div>
        <div class="form-group">
            <label for="">{{__('common.country')}}</label>
            <select name="country_id" required>
                @foreach($countries as $country)
                    <option value="{{$country->id}}"
                            @if($user->country_id == $country->id) selected @endif>{{$country->code}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="">{{__('crisis.language')}}</label>
            <select name="language_id" required>
                @foreach($languages as $language)
                    <option value="{{$language->id}}"
                            @if($user->language_id == $language->id) selected @endif>{{$language->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="">{{__('common.operator-connected-account')}}</label>
            <select name="connected_account" >
                <option value={{null}}>{{__('common.please-choose')}}</option>
                @foreach($users as $u)
                  <option @if($u->id == $user->connected_account) selected @endif value="{{$u->id}}">{{$u->username}}</option>
                @endforeach
            </select>
          </div>
        <div class="form-group">
            <button type="submit" class="btn-radius button btn-radius d-flex align-items-center" name="button"
            style="--btn-max-width: var(--btn-min-width); --btn-margin-left: 0px;">
                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">{{__('common.save')}}</button>
        </div>
    </form>
@endsection
