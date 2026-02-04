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
        @if($notification->users->count() == 0)
        $('.show-options.target_group select').chosen();
        @else
        $('.show-options.invidual_target select').chosen();
        @endif

        $('#datetimepicker1').datetimepicker({
            format: 'Y-MM-DD HH:mm:ss',
            defaultDate: '{{$notification->display_from}}',
            icons: {
                time: 'far fa-clock',
            }
        });

    </script>
@endsection

@section('content')
    {{ Breadcrumbs::render('notifications.edit', $notification->id) }}
    <h1>{{__('common.notification')}} {{__('common.edit')}}</h1>
    <form method="post" class="mb-5">
        {{csrf_field()}}
        <div class="form-group">
            <label for="">Megjelenítési dátum </label>
            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                <input type="text" class="form-control datetimepicker-input" id="datetimepicker1"
                       data-target="#datetimepicker1" data-toggle="datetimepicker" name="display_from" required
                       value="{{$notification->display_from}}" placeholder="Megjelenítési dátum" autocomplete="off"/>
                <div style="visibility:hidden;display:none;" class="input-group-append" data-target="#datetimepicker1"
                     data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
            </div>
        </div>
        <!--<input type="text" id="datetimepicker" name="display_from" required value="{{$notification->display_from}}" placeholder="Megjelenítési dátum" autocomplete="off">-->
        @foreach($languages as $language)
            <div class="form-group">
                <label for="">Értesítés szövege ({{$language->code}})</label>
                <textarea rows="5" name="text[{{$language->id}}]"
                          placeholder="Értesítés szövege ({{$language->code}})" autocomplete="off">{{$notification->allTranslations->firstWhere('language_id',$language->id) ? $notification->allTranslations->firstWhere('language_id',$language->id)->value : null }}
                </textarea>
            </div>
        @endforeach
        <div class="form-group">
            <label for="">Kinek jelenjen meg az értesítés?</label>
            <select name="show_for" required>
                <option value="">Kérjük, válassz!</option>
                <option value="invidual_target" @if($notification->users->count() > 0) selected @endif>Egyesével
                    kiválasztott felhasználók (pl egy cég felhasználói)
                </option>
                <option value="target_group" @if($notification->users->count() == 0) selected @endif>Felhasználók egy
                    csoportjának (pl. szakértők, operátorok, adminok stb.)
                </option>
            </select>
        </div>
        <div class="form-group show-options invidual_target @if($notification->users->count() == 0) d-none @endif">
            <label for="">Egyesével kiválasztott felhasználók (pl egy cég felhasználói)</label>
            <select name="selected_users[]" id="selected_users" multiple class="chosen-select"
                    data-placeholder="Felhasználók">
                @foreach($users as $user)
                    {{--              <option value="{{$user->id}}" @if($notification->users && in_array($user->id,$notification->users->pluck('id')->toArray())) selected @endif>{{$user->name}} ({{$user->type}}) - {{$user->email}} - {{$user->country->code}}</option>--}}
                    <option value="{{$user->id}}"
                            @if($notification->users && in_array($user->id,$notification->users->pluck('id')->toArray())) selected @endif>{{$user->name}}
                        ({{$user->type}}) - {{$user->email}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group show-options target_group @if($notification->users->count() > 0) d-none @endif">
            <label for="">Felhasználók egy csoportjának (pl. szakértők, operátorok, adminok etc)</label>
            <select name="selected_target_groups[]" id="selected_target_groups" multiple class="chosen-select"
                    data-placeholder="Felhasználói csoportok">
                <option value="admin"
                        @if($notification->groupTarget && $notification->groupTarget->userTypes && in_array('admin',$notification->groupTarget->userTypes->pluck('type')->toArray())) selected @endif>
                    Adminok
                </option>
                <option value="client"
                        @if($notification->groupTarget && $notification->groupTarget->userTypes && in_array('client',$notification->groupTarget->userTypes->pluck('type')->toArray())) selected @endif>
                    Ügyfelek
                </option>
                <option value="production_admin"
                        @if($notification->groupTarget && $notification->groupTarget->userTypes && in_array('production_admin',$notification->groupTarget->userTypes->pluck('type')->toArray())) selected @endif>
                    Production Adminok
                </option>
                <option value="account_admin"
                        @if($notification->groupTarget && $notification->groupTarget->userTypes && in_array('account_admin',$notification->groupTarget->userTypes->pluck('type')->toArray())) selected @endif>
                    Account Adminok
                </option>
                <option value="eap_admin"
                        @if($notification->groupTarget && $notification->groupTarget->userTypes && in_array('eap_admin',$notification->groupTarget->userTypes->pluck('type')->toArray())) selected @endif>
                    Eap Adminok
                </option>
                <option value="supervisor_admin"
                        @if($notification->groupTarget && $notification->groupTarget->userTypes && in_array('supervisor_admin',$notification->groupTarget->userTypes->pluck('type')->toArray())) selected @endif>
                        Supervisor Adminok
                </option>
                <option value="financial_admin"
                        @if($notification->groupTarget && $notification->groupTarget->userTypes && in_array('financial_admin',$notification->groupTarget->userTypes->pluck('type')->toArray())) selected @endif>
                    Financial Adminok
                </option>
                <option value="operator"
                        @if($notification->groupTarget && $notification->groupTarget->userTypes && in_array('operator',$notification->groupTarget->userTypes->pluck('type')->toArray())) selected @endif>
                    Operátorok
                </option>
                <option value="expert"
                        @if($notification->groupTarget && $notification->groupTarget->userTypes && in_array('expert',$notification->groupTarget->userTypes->pluck('type')->toArray())) selected @endif>
                    Szakértők
                </option>
            </select>
        </div>
        <div class="form-group show-options target_group @if($notification->users->count() > 0) d-none @endif">
            <label for="">Országok</label>
            <select name="selected_target_group_countries[]" id="selected_target_groups" multiple class="chosen-select"
                    data-placeholder="Országok">
                @foreach($countries as $country)
                    <option value="{{$country->id}}"
                            @if($notification->groupTarget && $notification->groupTarget->countries && in_array($country->id,$notification->groupTarget->countries->pluck('country_id')->toArray())) selected @endif>{{$country->code}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group show-options target_group @if($notification->users->count() > 0) d-none @endif">
            <label for="">Jogosultságok</label>
            <select name="selected_target_group_permissions[]" id="selected_target_groups" multiple
                    class="chosen-select" data-placeholder="Jogosultságok">
                @foreach($permissions as $permission)
                    <option value="{{$permission->id}}"
                            @if($notification->groupTarget && $notification->groupTarget->permissions && in_array($permission->id,$notification->groupTarget->permissions->pluck('permission_id')->toArray())) selected @endif>{{$permission->slug}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="">Megtekintések</label>
            @if($notification->seen->count())
                <ul id="notification-seen">
                    @foreach(collect($notification->seen)->sortByDesc('id') as $seen)
                        @if(!empty($seen->user))
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{$seen->user->name}} - {{$seen->user->email}}
                                - {{$seen->created_at}}</li>
                        @endif
                    @endforeach
                </ul>
            @else
                <p id="no-seen">Nincs megtekintés!</p>
            @endif
        </div>
        <div class="form-group">
            <button class="btn-radius button btn-radius d-flex align-items-center" style="--btn-max-width: var(--btn-min-width)" type="submit">
                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                {{__('common.save')}}
            </button>
        </div>
    </form>
@endsection
