@push('livewire_js')
<script src="{{asset('assets/js/datetime.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>
<script type="text/javascript">
        function uploadAttachments() {
            $('#attachments').trigger('click');
        }

        window.livewire.on('commentSaved', () => {
            $('#comment-modal').modal('hide');
        });

        window.livewire.on('userConnected', () => {
            $('#connect-user-modal').modal('hide');
        });

        document.addEventListener('livewire:load', function () {
            const editor = SUNEDITOR.create(document.getElementById('editor'), {
                height: 300,
                value: @this.task.description,
            });

            editor.onBlur = function (e, core) {
                @this.set('task.description', editor.getContents());
            }
        });

        $('#deadline').datepicker({
            format: 'yyyy-mm-dd',
            weekStart: 1,
            daysOfWeekDisabled: [0,6],
        }).on('changeDate', function(e){
            @this.set('task.deadline', e.format('yyyy-mm-dd'));
            $('.datepicker').hide();
        });

        window.livewire.on('alert', (data) => {
            Swal.fire(
                data.message,
                '',
                'success'
            );
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
        {{ Breadcrumbs::render('todo.edit', $task) }}
        <h1>{{__('task.edit')}} - #TD{{$task->id}}</h1>
    </div>
    <form>
        <div class="col-12 col-sm-12">
            <div class="form-group">
                <label for="title">{{__('task.title')}}:</label>
                <input  @if($task->status == \App\Models\Task::STATUS_COMPLETED) readonly @endif type="text" id="title" name="title" placeholder="{{__('task.title')}}" wire:model.lazy='task.title'>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="to_id">{{__('task.colleague')}}:</label>
                    <select name="to_id" id="to_id" wire:model.lazy='task.to_id'>
                        @foreach($admins as $admin)
                            <option wire:key="admin-{{ $admin->id }}" value="{{$admin->id}}">{{$admin->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row col-md-6">
                    <div wire:ignore class="form-group col-md-6">
                        <label for="deadline">{{__('task.deadline')}}:</label>
                        <input class="w-full" type="text" name="deadline" id="deadline"  placeholder="{{__('task.deadline')}}"
                        value={{\Carbon\Carbon::parse($task->deadline)->format('Y-m-d')}}>
                    </div>
                    <div wire:ignore class="form-group col-md-6">
                        <label for="deadline">{{__('task.created_at')}}:</label>
                        <input class="w-full" disabled value={{\Carbon\Carbon::parse($task->created_at)->format('Y-m-d')}}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6 mb-4">
                    <label for="country_id">{{__('task.connected-users')}}:</label>

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
                <textarea  @if($task->status == \App\Models\Task::STATUS_COMPLETED) readonly @endif name="description" id="editor" wire:model.lazy='task.description'></textarea>
            </div>

            <div class="form-group">
                <input wire:model='newAttachments' class="d-none" type="file" id="attachments" placeholder="Choose files" multiple >
            </div>
        </div>

        <div class="col-12 mt-2">
            @foreach($task->attachments as $attachment)
                <p wire:key="attachment-{{ $attachment->id }}">
                    <a href="{{route(auth()->user()->type . '.todo.download-attachment', ['id' => $attachment->id])}}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 mb-1" style="height:20px; width: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{$attachment->filename}}
                    </a>
                    <svg wire:click="deleteAttachment({{$attachment->id}})" xmlns="http://www.w3.org/2000/svg" class="ml-2" style="width: 20px; height:20px; cursor:pointer; margin-bottom: 2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </p>
            @endforeach
        </div>

        <div class="form-group col-12 d-flex pr-0 @if($task->comments->count() <= 0) justify-content-between @else justify-content-end @endif align-items-center">
            @if($task->comments->count() <= 0)
                <a

                style="text-transform: uppercase; font-weight: bold; color: rgb(0, 87, 93); background:transparent; border:none; cursor: pointer;"
                wire:click="backToList"
                >
                    {{__('common.back-to-list')}}
                </a>
            @endif

            <div class="mt-1 d-flex justify-content-end flex-wrap" style="text-transform: uppercase !important;">
                @if($task->status == \App\Models\Task::STATUS_COMPLETED)
                    <a wire:click='reopen' class="button btn-radius d-flex" style="background: rgb(127, 64, 116) !important; padding: 10px 40px;
                        color: white;" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1"fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                          </svg>
                        {{__('task.reopen')}}
                    </a>
                @endif

                @if($task->status == \App\Models\Task::STATUS_COMPLETED && !$task->confirmed)
                    <a wire:click='confirm' class="button btn-radius" style="background: rgb(89, 198, 198) !important; padding: 10px 40px; border:none;
                        color: white;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    {{__('task.confirm')}}
                    </a>
                @endif

                <a wire:click='save' class="button btn-radius" style="background: rgb(89, 198, 198) !important; padding: 10px 40px; border:none;
                    color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                      </svg>
                    {{__('common.save')}}
                </a>

                <a onClick="uploadAttachments()" class="button float-right btn-radius" style="background: rgb(89, 198, 198) !important; padding: 10px 40px; border:none;
                color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    {{__('task.attach_file')}}
                </a>

                @if($task->comments->count() <= 0)
                    <button data-toggle="modal" data-target="#comment-modal" type="button"
                            class="button btn-radius float-right" style="background: rgb(89, 198, 198) !important; padding: 10px 40px; border:none; width:auto; text-transform: uppercase !important;
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

    @if($task->comments->count())
        <div class="col-12 case-details">
            <h1>{{__('task.messages')}}</h1>
            <ul class="col-12 row m-0 p-0">
                @foreach($task->comments->sortBy('created_at') as $comment)
                    <li wire:key="comment-{{ $comment->id }}" class="col-12 {{$loop->last ? 'mb-5' : ''}} {{$loop->first ? 'mt-2' : ''}}" @if(!$comment->is_from_creator()) style="background: rgba(163 ,48 ,150 , 0.2)" @endif>
                        <div class="d-flex justify-content-between">
                            <p>{{__('common.from')}}: {{$comment->user->name}}</p>
                            <p>{{$comment->created_at}}</p>
                        </div>
                        <br>
                        <p style="font-family: CalibriI; font-weight: normal;">{!! nl2br($comment->value) !!}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="col-12 justify-content-between mb-5 align-items-center">
        @if($task->comments->count() > 0)
            <button
                style="text-transform: uppercase; font-weight: bold; color: rgb(0, 87, 93); margin-left: -5px; background:transparent; border:none;"
                wire:click="backToList"
            >
                {{__('common.back-to-list')}}
            </button>
        @endif

        @if($task->comments->count())
            <button data-toggle="modal" data-target="#comment-modal"
                class="button btn-radius float-right" style="background: rgb(89, 198, 198) !important; padding: 10px 40px; border:none; text-transform: uppercase !important;
                color: white;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                {{__('task.message')}}
            </button>
        @endif
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
                        <div>
                            <button class="btn-radius float-right" style="--btn-margin-right: 0px;" type="submit">
                                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                                <span class="mt-1">{{__('common.save')}}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
                        <div>
                            <button class="btn-radius float-right" type="submit" style="--btn-margin-right: 0px;">
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
