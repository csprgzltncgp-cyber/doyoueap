@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/notifications.css?v={{time()}}">
    <link rel="stylesheet" type="text/css"
          href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css">
    <link href="/assets/css/chosen.css" rel="stylesheet" type="text/css">
    <style>
        .chosen-container-multi .chosen-choices .search-field {
            width: 100%;
        }
    </style>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css"/>
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script src="/assets/js/moments.js" charset="utf-8"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="/assets/js/notifications.js?v={{time()}}" charset="utf-8"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js" type="text/javascript"
            charset="utf-8"></script>
    <script src="/assets/js/chosen.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $('#datetimepicker1').datetimepicker({
            format: 'Y-MM-DD HH:mm:ss',
            icons: {
                time: 'far fa-clock',
            }
        });
    </script>
@endsection

@section('content')
    {{ Breadcrumbs::render('notifications.create') }}
    <h1>{{__('common.create-notification')}}</h1>
    <form method="post" class="mb-5">
    {{csrf_field()}}
    <!--<div class="form-group">
      <label for="">Megjelenítési dátum </label>
      <input type="text" name="display_from" required value="{{\Carbon\Carbon::now()}}" placeholder="Megjelenítési dátum " autocomplete="off">
    </div>-->
        <div class="form-group">
            <label for="">Megjelenítési dátum </label>
            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                <input type="text" class="form-control datetimepicker-input" id="datetimepicker1"
                       data-target="#datetimepicker1" data-toggle="datetimepicker" name="display_from" required
                       placeholder="Megjelenítési dátum" autocomplete="off"/>
                <div style="visibility:hidden;display:none;" class="input-group-append" data-target="#datetimepicker1"
                     data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
            </div>
        </div>
        @foreach($languages as $language)
            <div class="form-group">
                <label for="">Értesítés szövege ({{$language->code}})</label>
                <textarea rows="5" name="text[{{$language->id}}]" value=""
                          placeholder="Értesítés szövege ({{$language->code}})" autocomplete="off"></textarea>
            </div>
        @endforeach
        <div class="form-group">
            <label for="">Kinek jelenjen meg az értesítés?</label>
            <select name="show_for" required>
                <option value="">Kérjük, válassz!</option>
                <option value="invidual_target">Egyesével kiválasztott felhasználók (pl egy cég felhasználói)</option>
                <option value="target_group">Felhasználók egy csoportjának (pl. szakértők, operátorok, adminok stb.)
                </option>
            </select>
        </div>
        <div class="form-group show-options invidual_target d-none">
            <label for="">Egyesével kiválasztott felhasználók (pl egy cég felhasználói)</label>
            <select name="selected_users[]" id="selected_users" multiple class="chosen-select"
                    data-placeholder="Felhasználók">
                @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->name}} ({{$user->type}}) - {{$user->email}}</option>
                    {{--              <option value="{{$user->id}}">{{$user->name}} ({{$user->type}}) - {{$user->email}} - {{$user->country->code}}</option>--}}
                @endforeach
            </select>
        </div>
        <div class="form-group show-options target_group d-none">
            <label for="">Felhasználók egy csoportjának (pl. szakértők, operátorok, adminok etc)</label>
            <select name="selected_target_groups[]" id="selected_target_groups" multiple class="chosen-select"
                    data-placeholder="Felhasználói csoportok">
                <option value="admin">Adminok</option>
                <option value="production_admin">Production Adminok</option>
                <option value="account_admin">Account Adminok</option>
                <option value="financial_admin">Financial Adminok</option>
                <option value="eap_admin">Eap Adminok</option>
                <option value="supervisor_admin">Supervisor Adminok</option>
                <option value="client">Ügyfelek</option>
                <option value="operator">Operátorok</option>
                <option value="expert">Szakértők</option>
            </select>
        </div>
        <div class="form-group show-options target_group d-none">
            <label for="">Országok</label>
            <select name="selected_target_group_countries[]" id="selected_target_groups" multiple class="chosen-select"
                    data-placeholder="Országok">
                @foreach($countries as $country)
                    <option value="{{$country->id}}">{{$country->code}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group permissions target_group d-none">
            <label for="">Szakértő típusok</label>
            <select name="selected_target_group_permissions[]" id="selected_target_groups" multiple
                    class="chosen-select" data-placeholder="Szakértő típusok">
                @foreach($permissions as $permission)
                    <option value="{{$permission->id}}">{{$permission->slug}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <button class="btn-radius button btn-radius d-flex align-items-center" style="--btn-max-width: var(--btn-min-width)" type="submit">
                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                     style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Létrehozás
            </button>
        </div>
    </form>
@endsection
