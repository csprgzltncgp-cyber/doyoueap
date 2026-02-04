@push('livewire_js')
<script src="{{asset('assets/js/datetime.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>
<script src="{{asset('assets/js/moments.js')}}"></script>
<script src="{{asset('assets/js/moment-business-days.js')}}"></script>
<script>
    window.livewire.on('commentSaved', () => {
        $('#comment-modal').modal('hide');
    });

    window.livewire.on('userConnected', () => {
        $('#connect-user-modal').modal('hide');
    });

    window.livewire.on('alert', (data) => {
        Swal.fire(
            data.message,
            '',
            'success'
        );
    });

    document.addEventListener('livewire:load', function () {
        if(parseInt(@this.get('affiliateSearch.deadline_type')) != {{App\Models\AffiliateSearch::DEADLINE_TYPE_SELECT_DATE}}){
            $('#deadline').css('pointer-events','none').css('opacity', '0.4');
        }

        const editor = SUNEDITOR.create(document.getElementById('editor'), {
            height: 300,
            value: @this.affiliateSearch.description,
        });

        editor.onBlur = function (e, core) {
            @this.set('affiliateSearch.description', editor.getContents());
        }
    });

    $('#deadline').datepicker({
        format: 'yyyy-mm-dd',
        weekStart: 1,
        daysOfWeekDisabled: [0,6],
    }).on('changeDate', function(e){
        @this.set('affiliateSearch.deadline', e.format('yyyy-mm-dd'));
        $('.datepicker').hide();
    });

    $('#deadline_type').on('change', function (e) {
        let value = $(this).find("option:selected").val();

        switch (parseInt(value)) {
            case {{App\Models\AffiliateSearch::DEADLINE_TYPE_SOS}}:
                $('#deadline').css('pointer-events','none').css('opacity', '0.4');
                $('#deadline').datepicker('setDate', moment().businessAdd(3, 'days').format('YYYY-MM-DD'));
                @this.set('affiliateSearch.deadline',moment().businessAdd(3, 'days').format('YYYY-MM-DD'));
                break;

            case {{App\Models\AffiliateSearch::DEADLINE_TYPE_WITHIN_A_WEEK}}:
                $('#deadline').css('pointer-events','none').css('opacity', '0.4');
                $('#deadline').datepicker('setDate', moment().businessAdd(7, 'days').format('YYYY-MM-DD'));
                @this.set('affiliateSearch.deadline',moment().businessAdd(7, 'days').format('YYYY-MM-DD'));
                break;

            case {{App\Models\AffiliateSearch::DEADLINE_TYPE_WITHIN_TWO_WEEKS}}:
                $('#deadline').css('pointer-events','none').css('opacity', '0.4');
                $('#deadline').datepicker('setDate', moment().businessAdd(14, 'days').format('YYYY-MM-DD'));
                @this.set('affiliateSearch.deadline',moment().businessAdd(14, 'days').format('YYYY-MM-DD'));
                break;

            case {{App\Models\AffiliateSearch::DEADLINE_TYPE_SELECT_DATE}}:
                $('#deadline').css('pointer-events','all').css('opacity', '1');
                break;
        }
    });
</script>
@endpush

<div>
    @section('title') ADMIN DASHBOARD @endsection
    <link rel="stylesheet" href="{{asset('assets/css/form.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/view.css') . '?t=' . time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/datetime.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
    <style media="screen">
        form {
            max-width: none;
        }
    </style>

    <div class="col-12">
        {{Breadcrumbs::render('affiliate-search-workflow.edit', $affiliateSearch)}}
        <h1>{{__('affiliate-search-workflow.edit')}} - #AS{{$affiliateSearch->id}}</h1>
    </div>
    <form wire:submit.prevent>
        <div class="col-12">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="affiliate_type">{{__('affiliate-search-workflow.affiliate_type')}}:</label>
                    <select id="affiliate_type" name="affiliate_type" wire:model.lazy='affiliateSearch.permission_id'>
                        @foreach($permissions as $permission)
                            <option value="{{$permission->id}}">{{$permission->translation->value}}</option>
                        @endforeach
                    </select>
                </div>

                <div wire:ignore class="form-group col-md-6">
                    <label for="deadline">{{__('task.created_at')}}:</label>
                    <input class="w-full" disabled value={{\Carbon\Carbon::parse($affiliateSearch->created_at)->format('Y-m-d')}}>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="to_id">{{__('task.colleague')}}:</label>
                    <select name="to_id" id="to_id" wire:model.lazy='affiliateSearch.to_id'>
                        @foreach($admins as $admin)
                            <option value="{{$admin->id}}">{{$admin->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div wire:ignore class="form-group col-md-6">
                    <label for="deadline">{{__('task.deadline')}}:</label>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <select wire:model.lazy='affiliateSearch.deadline_type' class="col-12" id="deadline_type" name="deadline_type">
                                <option value="{{App\Models\AffiliateSearch::DEADLINE_TYPE_SOS}}" >{{__("affiliate-search-workflow.deadline_type." . App\Models\AffiliateSearch::DEADLINE_TYPE_SOS)}}</option>
                                <option value="{{App\Models\AffiliateSearch::DEADLINE_TYPE_WITHIN_A_WEEK}}" >{{__("affiliate-search-workflow.deadline_type." . App\Models\AffiliateSearch::DEADLINE_TYPE_WITHIN_A_WEEK)}}</option>
                                <option value="{{App\Models\AffiliateSearch::DEADLINE_TYPE_WITHIN_TWO_WEEKS}}" >{{__("affiliate-search-workflow.deadline_type." . App\Models\AffiliateSearch::DEADLINE_TYPE_WITHIN_TWO_WEEKS)}}</option>
                                <option value="{{App\Models\AffiliateSearch::DEADLINE_TYPE_SELECT_DATE}}" >{{__("affiliate-search-workflow.deadline_type." . App\Models\AffiliateSearch::DEADLINE_TYPE_SELECT_DATE)}}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" name="deadline" id="deadline"  placeholder="{{__('task.deadline')}}"
                                value={{\Carbon\Carbon::parse($affiliateSearch->deadline)->format('Y-m-d')}}
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="country_id">{{__('common.country')}}:</label>
                    <select name="country_id" id="country_id" wire:model.lazy='affiliateSearch.country_id'>
                        @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="city_id">{{__('crisis.city')}}:</label>
                    <select name="city_id" id="city_id" wire:model.lazy='affiliateSearch.city_id'>
                        <option value="null" disabled>{{__('crisis.select')}}</option>
                        @foreach($cities as $city)
                            <option value="{{$city->id}}">{{$city->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6 mb-4">
                    <label for="country_id">{{__('affiliate-search-workflow.connected-users')}}:</label>

                    @foreach ($connected_users as $connected_user)
                        <div class="d-flex align-items-center">
                            <input type="text" disabled readonly value="{{$connected_user->name}}">
                            <svg wire:click="detachConnectedUser({{$connected_user->id}})" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 20px; margin-left:10px; height: 24px; width:24px; color: rgb(89, 198, 198); cursor: pointer;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                    @endforeach

                    <div class="button btn-radius" data-toggle="modal" data-target="#connect-user-modal"
                        style="--btn-min-width: auto; background: rgb(89, 198, 198) !important; padding: 5px; border:none; text-transform: uppercase !important; width:max-content; cursor: pointer;
                        color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                    </div>
                </div>
            </div>

            <div class="form-group" wire:ignore>
                <label for="editor">{{__('task.description')}}:</label>
                <textarea name="description" id="editor" wire:model.lazy='affiliateSearch.description'></textarea>
            </div>
        </div>

        <div class="col-12 mt-3">
            @foreach($affiliateSearch->attachments as $attachment)
                <p>
                    <a href="{{route(auth()->user()->type . '.affiliate_searches.download-attachment', ['id' => $attachment->id])}}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 mb-1" style="height:20px; width: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{$attachment->filename}}
                    </a>
                </p>
            @endforeach
        </div>

        <x-affiliate-search.status-buttons :affiliateSearch="$affiliateSearch"/>

        <div class="col-12 d-flex @if(!$affiliateSearch->comments->count()) justify-content-between @else justify-content-end @endif align-items-center">
            @if(!$affiliateSearch->comments->count())
                <a
                    style="text-transform: uppercase; font-weight: bold; color: rgb(0, 87, 93); background:transparent; border:none; cursor: pointer;"
                    wire:click="backToList"
                >
                    {{__('common.back-to-list')}}
                </a>
            @endif

            <div class="flex mt-3 uppercase" style="text-transform: uppercase !important;">
                @if($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_COMPLETED)
                    <a wire:click='reopen' class="button btn-radius float-right" style="--btn-margin-right: 0px; background: rgb(127, 64, 116) !important; padding: 10px 40px;
                        color: white;" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1"fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{__('task.reopen')}}
                    </a>
                @endif

                @if($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_COMPLETED && !$affiliateSearch->completed)
                    <a wire:click='confirm' class="button btn-radius float-right" style="background: rgb(89, 198, 198) !important; padding: 10px 40px; border:none;
                        color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        {{__('task.confirm')}}
                    </a>
                @endif

                <a wire:click='save' class="button btn-radius float-right" style="background: rgb(89, 198, 198) !important; padding: 10px 40px; border:none;
                    color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    {{__('common.save')}}
                </a>

                @if(!$affiliateSearch->comments->count())
                    <button data-toggle="modal" data-target="#comment-modal" type="button"
                            class="button btn-radius float-right mr-2" style="background: rgb(89, 198, 198) !important; padding: 10px 40px; border:none; width:auto; text-transform: uppercase !important;
                            color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            {{__('task.message')}}
                    </button>
                @endif
            </div>
        </div>
    </form>

    @if($affiliateSearch->comments->count())
        <div class="col-12 case-details">
            <h1>{{__('task.messages')}}</h1>
            <ul>
                @foreach($affiliateSearch->comments->sortBy('created_at') as $comment)
                    <li class="col-12 {{$loop->last ? 'mb-5' : ''}} {{$loop->first ? 'mt-2' : ''}}" @if(!$comment->is_from_creator()) style="background: rgba(163 ,48 ,150 , 0.2)" @endif>
                        <div class="d-flex justify-content-between">
                            <p>{{__('common.from')}}: {{$comment->user->name}}</p>
                            <p>{{$comment->created_at}}</p>
                        </div>
                        <br>
                        <p style="font-family: CalibriI; font-weight: normal;">{!! $comment->value !!}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($affiliateSearch->comments->count())
        <div class="col-12 justify-content-between mb-5 align-items-center">
            <button
                style="text-transform: uppercase; font-weight: bold; color: rgb(0, 87, 93); margin-left: -5px; background:transparent; border:none;"
                wire:click="backToList"
            >
                {{__('common.back-to-list')}}
            </button>

            <button data-toggle="modal" data-target="#comment-modal"
                class="button btn-radius float-right" style="background: rgb(89, 198, 198) !important; padding: 10px 40px; border:none; text-transform: uppercase !important;
                color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    {{__('task.message')}}
            </button>
        </div>
    @endif


    <div wire:ignore.self class="modal" tabindex="-1" id="comment-modal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('task.comment')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent='saveComment'  style="margin-top:0">
                        <textarea wire:model.defer='newComment' cols="30" rows="10" placeholder="{{__('task.message')}}"></textarea>
                        <div class="d-flex">
                            <button class="btn-radius" type="submit" style="width:auto">
                                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                                <span class="mt-1">{{__('common.save')}}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
                    <form wire:submit.prevent='connectUser' style="margin-top:0">
                        <select wire:model.defer='newConnectedUser'>
                            <option value="null" disabled>{{__('crisis.select')}}</option>
                            @foreach($connectable_users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                        <div class="d-flex">
                            <button class="btn-radius" type="submit" style="width:auto">
                                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                                <span class="mt-1">{{__('common.save')}}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
