@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/form.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
    <style media="screen">
        form {
            max-width: none;
        }
    </style>
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/datetime.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>
    <script>
        const editor = SUNEDITOR.create(document.getElementById('editor'), {
            height: 300,
        });

        document.querySelector('form').addEventListener('submit', function(e){
            editor.save();
        });

        $('#deadline').datepicker({
            format: 'yyyy-mm-dd',
            weekStart: 1,
            daysOfWeekDisabled: [0,6],
            startDate: '0d'
        }).on('changeDate', function(e){
            $('.datepicker').hide();
        });

        $('#attachments').on('change', function() {
            $('#attachments-list').html('');
            var files = $(this)[0].files;
            var names = $.map(files, function(val) { return val.name; });
            names.forEach(function(name) {
                $('#attachments-list').append(
                    '<p>' +
                    `<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                     </svg>` +
                     name +
                     '</p>'
                );
            });
        });

        $('#connect_user_form').on('submit', function(e){
            e.preventDefault();
            const selected_user_id = $('#connect_user_form').find('select').val();
            const selected_user_name = $('#connect_user_form').find('select option:selected').text();



            $('#connected_user_holder').append(`
                        <div class="d-flex align-items-center" id="connected-user-${selected_user_id}">
                            <input type="text" readonly value="${selected_user_name}">
                            <input type="hidden" name="connected_users[]" value="${selected_user_id}">
                            <svg onclick="removeConnectedUser(${selected_user_id})" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 20px; margin-left:10px; height: 24px; width:24px; color: rgb(89, 198, 198); cursor: pointer;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
            `);

            $('#connect-user-modal').modal('hide');
        });

        function removeConnectedUser(id){
            $(`#connected-user-${id}`).remove();
        }

        function uploadAttachments() {
            $('#attachments').trigger('click');
        }
    </script>
@endsection

@section('content')
    {{ Breadcrumbs::render('todo.create') }}
    <h1>{{__('task.create')}}</h1>
    <form method="post" class="row" enctype="multipart/form-data">
        @csrf
        <div class="col-12 col-sm-12">
            <div class="form-group">
                <label for="title">{{__('task.title')}}:</label>
                <input type="text" id="title" name="title" placeholder="{{__('task.title')}}" value="{{$forwarded_title}}">
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="to_id">{{__('task.colleague')}}:</label>
                    <select name="to_id" id="to_id">
                        @foreach($admins as $admin)
                            <option value="{{$admin->id}}">{{$admin->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="deadline">{{__('task.deadline')}}:</label>
                    <input type="text" name="deadline" id="deadline" placeholder="{{__('task.deadline')}}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6 mb-4">
                    <label for="country_id">{{__('task.connected-users')}}:</label>

                    <div id="connected_user_holder">

                    </div>

                    <div class="button btn-radius" data-toggle="modal" data-target="#connect-user-modal"
                        class="button" style="--btn-min-width: auto; background: rgb(89, 198, 198) !important; padding: 5px; border:none; text-transform: uppercase !important; width:max-content; cursor: pointer;
                        color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="editor">{{__('task.description')}}:</label>
                <textarea name="description" id="editor">{{$forwarded_description}}</textarea>
            </div>

            <div class="form-group">
                <input wire:model='attachments' class="d-none" type="file" name="attachments[]" id="attachments" placeholder="Choose files" multiple >
                <div id="attachments-list"></div>
            </div>
        </div>


        <div class="col-12 d-flex justify-content-end">
            <div class="form-group">
                <button type="button" onClick="uploadAttachments()" class="btn-radius float-right">
                      <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                      </svg>
                    {{__('task.attach_file')}}
                </button>
            </div>
            <div class="form-group">
                <button type="submit" style="--btn-margin-right: 0px;" class="btn-radius">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                      </svg>
                    {{__('common.save')}}
                </button>
            </div>
        </div>
    </form>
@endsection

@section('modal')
<div wire:ignore.self class="modal" tabindex="-1" id="connect-user-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('affiliate-search-workflow.connect-user')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="connect_user_form" style="margin-top:0">
                    <select wire:model.defer='newConnectedUser'>
                        <option value="null" disabled>{{__('crisis.select')}}</option>
                        @foreach($connectable_users as $user)
                            <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                    </select>
                    <div>
                        <button class="btn-radius float-right" style="--btn-margin-right: 0px;" type="submit" style="width:auto">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">{{__('common.save')}}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
