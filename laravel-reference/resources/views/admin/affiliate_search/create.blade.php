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
    <script src="{{asset('assets/js/moments.js')}}"></script>
    <script src="{{asset('assets/js/moment-business-days.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>
    <script>
        const editor = SUNEDITOR.create(document.getElementById('editor'), {
            height: 300,
        });

        document.querySelector('form').addEventListener('submit', function(e){
            editor.save();
        });

        $(document).ready(function () {
            hideCityOptions();
        });


        $('#country').change(function () {
            hideCityOptions();
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

        $('#deadline').datepicker({
            format: 'yyyy-mm-dd',
            weekStart: 1,
            daysOfWeekDisabled: [0,6],
            startDate: '0d',
        }).datepicker("setDate", moment().businessAdd(3, 'days').format('YYYY-MM-DD'));

        $('#deadline_type').on('change', function (e) {
            let value = $(this).find("option:selected").val();

            switch (parseInt(value)) {
                case {{App\Models\AffiliateSearch::DEADLINE_TYPE_SOS}}:
                    $('#deadline').css('pointer-events','none').css('opacity', '0.4');
                    $('#deadline').datepicker('setDate', moment().businessAdd(3, 'days').format('YYYY-MM-DD'));
                    break;

                case {{App\Models\AffiliateSearch::DEADLINE_TYPE_WITHIN_A_WEEK}}:
                    $('#deadline').css('pointer-events','none').css('opacity', '0.4');
                    $('#deadline').datepicker('setDate', moment().businessAdd(7, 'days').format('YYYY-MM-DD'));
                    break;

                case {{App\Models\AffiliateSearch::DEADLINE_TYPE_WITHIN_TWO_WEEKS}}:
                    $('#deadline').css('pointer-events','none').css('opacity', '0.4');
                    $('#deadline').datepicker('setDate', moment().businessAdd(14, 'days').format('YYYY-MM-DD'));
                    break;

                case {{App\Models\AffiliateSearch::DEADLINE_TYPE_SELECT_DATE}}:
                    $('#deadline').css('pointer-events','all').css('opacity', '1');
                    break;
            }
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

        function hideCityOptions() {
            let country = $('#country').val();
            $('#city option').each(function () {
                if ($(this).data('country') != country) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });

            // select first placeholder option
            $('#city option:first-child').prop('selected', true);
        }

        function uploadAttachments() {
            $('#attachments').trigger('click');
        }
    </script>
@endsection


@section('content')
    {{Breadcrumbs::render('affiliate-search-workflow.create')}}
    <h1>{{__('affiliate-search-workflow.create')}}</h1>
    <form method="post" class="row" enctype="multipart/form-data">
        @csrf
        <div class="col-12 col-sm-12">
            <div class="form-group">
                <label for="permission_id">{{__('affiliate-search-workflow.affiliate_type')}}:</label>
                <select id="permission_id" name="permission_id">
                    @foreach($permissions as $permission)
                        <option @if($forwarded_permission == $permission->id) selected @endif value="{{$permission->id}}">{{$permission->translation->value}}</option>
                    @endforeach
                </select>
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
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <select class="col-12" id="deadline_type" name="deadline_type">
                                <option value="{{App\Models\AffiliateSearch::DEADLINE_TYPE_SOS}}" >{{__("affiliate-search-workflow.deadline_type." . App\Models\AffiliateSearch::DEADLINE_TYPE_SOS)}}</option>
                                <option value="{{App\Models\AffiliateSearch::DEADLINE_TYPE_WITHIN_A_WEEK}}" >{{__("affiliate-search-workflow.deadline_type." . App\Models\AffiliateSearch::DEADLINE_TYPE_WITHIN_A_WEEK)}}</option>
                                <option value="{{App\Models\AffiliateSearch::DEADLINE_TYPE_WITHIN_TWO_WEEKS}}" >{{__("affiliate-search-workflow.deadline_type." . App\Models\AffiliateSearch::DEADLINE_TYPE_WITHIN_TWO_WEEKS)}}</option>
                                <option value="{{App\Models\AffiliateSearch::DEADLINE_TYPE_SELECT_DATE}}" >{{__("affiliate-search-workflow.deadline_type." . App\Models\AffiliateSearch::DEADLINE_TYPE_SELECT_DATE)}}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <input class="col-12" type="text" name="deadline" id="deadline" placeholder="{{__('task.deadline')}}" style="opacity: 0.4; pointer-events:none;">
                        </div>
                    </div>
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

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="country">{{__('common.country')}}:</label>
                    <select name="country_id" id="country">
                        @foreach($countries as $country)
                            <option @if($forwarded_country == $country->id) selected @endif  value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="city">{{__('crisis.city')}}:</label>
                    <select name="city_id" id="city">
                        <option selected disabled>{{__('crisis.select')}}</option>
                        @foreach($cities as $city)
                            <option @if($forwarded_city == $city->id) selected @endif data-country="{{$city->country_id}}" value="{{$city->id}}">{{$city->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="editor">{{__('task.description')}}:</label>
                <textarea name="description" id="editor">{{$forwarded_description}}</textarea>
            </div>

            <div class="form-group">
                <input class="d-none" type="file" name="attachments[]" id="attachments" placeholder="Choose files" multiple >
                <div id="attachments-list"></div>
            </div>
        </div>


        <div class="col-12 d-flex justify-content-end">
            <div class="form-group">
                <button type="button" onClick="uploadAttachments()" class="float-right btn-radius">
                      <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                      </svg>
                    {{__('task.attach_file')}}
                </button>
            </div>
            <div class="form-group">
                <button type="submit" class="float-right btn-radius" style="--btn-margin-right: 0px;">
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
                    <div class="col p-0">
                        <button class="btn-radius float-right" style="--btn-margin-right: 0px;" type="submit" style="width:auto">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            {{__('common.save')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
