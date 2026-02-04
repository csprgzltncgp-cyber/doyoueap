@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script type="text/javascript">
        function deletePermission(element) {
            $(element).closest('.list-element').remove();
        }

        function addPermission() {
            var html = '<div class="list-element row">\
        <div class="col-3">\
          <select name="permission_id[]" required>\
            @foreach($permissions as $p)
                <option value="{{$p->id}}">{{$p->slug}}</option>\
            @endforeach
                        </select>\
                    </div>\
                    <div class="col-1">\
                        <input type="number" name="number[]" value="" required placeholder="{{__('common.count')}}">\
                    </div>\
                    <div class="col-1">\
                        <input type="number" name="duration[]" value="" required placeholder="{{__('common.period')}}">\
                    </div>\
                    <div class="col-4">\
                        <select name="contact[]">\
                            <option value="phone">{{__('common.phone')}}</option>\
                            <option value="personal">{{__('common.personal')}}</option>\
                            <option value="chat-video">{{__('common.chat-video')}}</option>\
                            <option value="chat-video-phone-personal">{{__('common.chat-video-phone-personal')}}</option>\
                            <option value="video-phone-personal">{{__('common.video-phone-personal')}}</option>\
                            <option value="video-phone">{{__('common.chat-video-phone')}}</option>\
                            <option value="video-personal">{{__('common.chat-video-personal')}}</option>\
                            <option value="phone-personal">{{__('common.phone-personal')}}</option>\
                            <option value="phone-chat-video">{{__('common.phone-email')}}</option>\
                        </select>\
                </div>\
                <div class="col-3">\
                <button type="button" class="text-center btn-radius" style="--btn-height: auto;" name="button" onClick="deletePermission(this)"><i class="fas fa-trash-alt"></i> {{__('common.delete')}}</button>\
                </div>\
            </div>';
                    $('#permissions-holder').append(html);
                }
            </script>
        @endsection

@section('content')
    <div class="col-12 pl-0">
        {{ Breadcrumbs::render('permissions.edit', $company) }}
        <h1>{{$company->name}}</h1>
    </div>
    <form method="post" class="col-12 pl-0">
        {{csrf_field()}}
        <div class="form-group">
            <div id="permissions-holder">
                @foreach($company->permissions as $key => $permission)
                    <div class="list-element row">
                        <div class="col-3">
                            <select name="permission_id[]" required>
                                @foreach($permissions as $p)
                                    <option value="{{$p->id}}"
                                            @if($permission->pivot->permission_id == $p->id) selected @endif>{{$p->translation->value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-1">
                            <input type="number" name="number[]" value="{{$permission->pivot->number}}" required
                                   placeholder="{{__('common.count')}}">
                        </div>
                        <div class="col-1">
                            <input type="number" name="duration[]" value="{{$permission->pivot->duration}}" required
                                   placeholder="{{__('common.period')}}">
                        </div>
                        <div class="col-4">
                            <select name="contact[]">
                                <option value="phone"
                                        @if($permission->pivot->contact == 'phone') selected @endif>{{__('common.phone')}}</option>
                                <option value="personal"
                                        @if($permission->pivot->contact == 'personal') selected @endif>{{__('common.personal')}}</option>
                                <option value="chat-video"
                                        @if($permission->pivot->contact == 'chat-video') selected @endif>{{__('common.chat-video')}}</option>
                                <option value="chat-video-phone-personal"
                                        @if($permission->pivot->contact == 'chat-video-phone-personal') selected @endif>{{__('common.chat-video-phone-personal')}}</option>
                                <option value="video-phone-personal"
                                        @if($permission->pivot->contact == 'video-phone-personal') selected @endif>{{__('common.video-phone-personal')}}</option>
                                <option value="video-phone"
                                        @if($permission->pivot->contact == 'chat-video-phone') selected @endif >{{__('common.chat-video-phone')}}</option>
                                <option value="video-personal"
                                        @if($permission->pivot->contact == 'chat-video-personal') selected @endif>{{__('common.chat-video-personal')}}</option>
                                <option value="phone-personal"
                                        @if($permission->pivot->contact == 'phone-personal') selected @endif>{{__('common.phone-personal')}}</option>
                                <option value="phone-chat-video"
                                        @if($permission->pivot->contact == 'phone-chat-video') selected @endif>{{__('common.phone-chat-video')}}</option>
                                <option value="phone-email"
                                        @if($permission->pivot->contact == 'phone-email') selected @endif>{{__('common.phone-email')}}</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <button type="button" class="text-center btn-radius" style="--btn-max-width: var(--btn-min-width); --btn-height: 48px;" name="button" onClick="deletePermission(this)"><i
                                        class="fas fa-trash-alt"></i>
                                <img src="{{asset('assets/img/delete.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                                <span class="mt-1">
                                    {{__('common.delete')}}
                                </span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-3 float-right">
                    <button type="button" name="button" class="mb-2 text-center btn-radius" style="--btn-max-width: var(--btn-min-width); --btn-margin-bottom: var(--btn-margin-y)" onClick="addPermission()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="heigth:20px; width:20px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg> {{__('common.add')}}</button>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <button type="submit" name="button" class="text-center btn-radius" style="--btn-max-width: var(--btn-min-width)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 mb-1"  style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        {{__('common.save')}}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
