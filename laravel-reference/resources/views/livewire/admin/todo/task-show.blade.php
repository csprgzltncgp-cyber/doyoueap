@push('livewire_js')
<script type="text/javascript">
    window.livewire.on('commentSaved', () => {
        $('#comment-modal').modal('hide');
    });

    window.livewire.on('reopened', () => {
        Swal.fire(
            '{{__('task.repoen_sussess')}}',
            '',
            'success'
        );
    });

    function completeTask(){
        Swal.fire({
            title: '{{__('task.are_you_suer_to_complete_task')}}',
            text: '',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{__('common.yes')}}',
            cancelButtonText: '{{__('common.no')}}'
        }).then((result) => {
            if (result.value) {
                window.livewire.emit('completeTask');
            }
        });
    }
</script>
@endpush


<div>
    <link rel="stylesheet" href="{{asset('assets/css/cases/view.css') . '?t=' . time()}}">
    <style>
        .description-field {
            display: flex;
            flex-direction: column;
            font-family: CalibriI !important;
        }
        .description-field ul{
            display: flex;
            flex-direction: column;
        }
        .description-field ul li{
            padding: 0 !important;
            list-style: inside !important;
            display: list-item;
        }

        .description-field ol{
            display: flex;
            flex-direction: column;
            padding: 0 !important;
        }
        .description-field ol li{
            padding: 0 !important;
            list-style: inside !important;
            display: list-item;
        }

        .description-field strong{
            font-family: CalibriB !important;
        }
    </style>

    @section('title') ADMIN DASHBOARD @endsection

    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('todo.show', $task) }}
            <h1>{{__('task.task')}}</h1>
        </div>

        <div class="col-12 case-title">
            <p>
                #TD{{$task->id}} -
                {{$task->from->name}} -
                {{\Illuminate\Support\Str::limit(strip_tags(html_entity_decode($task->title)), 55)}} -
                {{\Carbon\Carbon::parse($task->deadline)->format('Y-m-d')}}
            </p>
        </div>
        <div class="col-12 case-details mb-0">
            <ul>
                <li>
                    <span>
                        Id: <span>#TD{{$task->id}}</span>
                    </span>
                </li>
                <li>
                    <span>
                        {{__('task.title')}}: <span>{{$task->title}}</span>
                    </span>
                </li>
                <li>
                    <span>
                        {{__('task.colleague')}}: <span>{{$task->from->name}}</span>
                    </span>
                </li>
                <li>
                    <span>
                        {{__('task.deadline')}}: <span>{{\Carbon\Carbon::parse($task->deadline)->format('Y-m-d')}}</span>
                    </span>
                </li>
                <li class="col-12">
                    <span>
                        <p>{{__('task.description')}}:</p> <span  class="description-field"><br>{!! $task->description !!}</span>
                    </span>
                </li>
            </ul>
        </div>

        <div class="col-12 mt-2">
            @foreach($task->attachments as $attachment)
                <p>
                    <a href="{{route(auth()->user()->type . '.todo.download-attachment', ['id' => $attachment->id])}}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 mb-1" style="height:20px; width: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{$attachment->filename}}
                    </a>
                </p>
            @endforeach
        </div>

        @if($task->comments->count())
            <div class="col-12 case-details">
                <h1>{{__('task.messages')}}</h1>
                <ul>
                    @foreach($task->comments->sortBy('created_at') as $comment)
                        <li class="col-12" @if(!$comment->is_from_creator()) style="background: rgba(163 ,48 ,150 , 0.2)" @endif>
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

        <div class="col-12 button-holder d-flex justify-content-between mb-5 mt-5">
            <button
                style="text-transform: uppercase; font-weight: bold; color: rgb(0, 87, 93); margin-left: -5px; background:transparent; border:none;"
                wire:click="backToList"
                >
                    {{__('common.back-to-list')}}
            </button>

            <div class="row">
                <button data-toggle="modal" data-target="#comment-modal" class="button btn-radius">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    {{__('task.message')}}
                </button>

                @if(in_array(auth()->id(), [$task->from_id, $task->to_id]))
                    <button wire:click="forwardTask" class="button mr-2 btn-radius">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.933 12.8a1 1 0 000-1.6L6.6 7.2A1 1 0 005 8v8a1 1 0 001.6.8l5.333-4zM19.933 12.8a1 1 0 000-1.6l-5.333-4A1 1 0 0013 8v8a1 1 0 001.6.8l5.333-4z" />
                        </svg>
                        {{__('task.forward')}}
                    </button>

                    @if($task->status != \App\Models\Task::STATUS_COMPLETED)
                        <a onClick="completeTask()" class="button btn-radius d-flex">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                style="width: 20px; height: 20px; margin-bottom: 4px;" class="mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{__('task.completed')}}
                        </a>
                    @else
                        <a wire:click='reopen' class="button btn-radius d-flex" style="background: rgb(127, 64, 116) !important;
                            color: white;" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="mr-1"fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{__('task.reopen')}}
                        </a>
                    @endif
                @endif
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
                    <form wire:submit.prevent='saveComment'>
                        <textarea wire:model.defer='newComment' cols="30" rows="10" placeholder="{{__('task.message')}}"></textarea>
                        <button class="button btn-radius btn-max-width float-right d-flex justify-content-center align-items-center" type="submit">
                            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            {{__('common.save')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
