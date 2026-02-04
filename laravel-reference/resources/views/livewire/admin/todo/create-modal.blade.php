@push('livewire_js')
<script src="{{asset('js/client/master.js')}}?v={{time()}}"></script>
<script src="{{asset('assets/js/datetime.js')}}"></script>
@endpush
<div>
    <link rel="stylesheet" href="{{asset('assets/css/client/master.css')}}?v={{time()}}">
    <div class="w-full flex flex-col items-center justify-center mt-5">
        <h1 class="text-2xl">{{__('task.create')}}</h1>
        <h3 class="text-xl">({{$start_date}})</h3>
    </div>
    <form wire:submit.prevent class="p-3 flex flex-col" style="max-width: none !important;" enctype="multipart/form-data">
        <div class="w-full">
            <div class="mb-3">
                <label for="title">{{__('task.title')}}:</label>
                <input wire:model="task.title" class="task-input !mb-0" type="text" id="title" name="title" placeholder="{{__('task.title')}}" value="">
            </div>

            <div>
                <div class="mb-3">
                    <label for="to_id">{{__('task.colleague')}}:</label>
                    <select wire:model="task.to_id" name="to_id" id="to_id">
                        @foreach($admins as $admin)
                            <option value="{{$admin->id}}">{{$admin->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="deadline">{{__('task.deadline')}}:</label>
                    <input wire:model="task.deadline" class="task-input" autocomplete="off" type="text" name="deadline" id="deadline" placeholder="{{__('task.deadline')}}">
                </div>
            </div>
            <div class="flex">
                <div class="flex flex-column justify-content-start">
                    <label for="country_id">{{__('task.connected-users')}}:</label>

                    <div id="connected_user_holder">
                        @foreach ($connected_users as $key => $connected_user)
                            <div class="flex flex-row items-center mb-3">
                                <input type="text" class="!mb-0" readonly value="{{$connected_user['name']}}">
                                <svg wire:click="remove_connected_user({{$key}})" xmlns="http://www.w3.org/2000/svg" class="w-6 text-green-light cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                        @endforeach
                    </div>
                    <div class="button btn-radius @if($show_connected_user_select) hidden @endif bg-green-light text-white cursor-pointer" wire:click="show_user_select()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{__('common.add')}}
                    </div>
                </div>
            </div>
            <div class="@if($show_connected_user_select) flex @else hidden @endif">
                <select wire:model='new_connected_user'>
                    <option value="null" disabled>{{__('crisis.select')}}</option>
                    @foreach($connectable_users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>
                <button wire:click="add_connected_user()"  class="btn-radius bg-green-light text-white ml-3 !h-[48px] cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 w-5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{__('common.add')}}
                </button>
                <button wire:click="show_user_select()" class="btn-radius bg-green-light text-white !h-[48px] !mr-0 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{__('common.cancel')}}
                </button>
            </div>

            <div wire:ignore >
                <label for="editor">{{__('task.description')}}:</label>
                <textarea wire:ignore class="task-input" name="description" id="editor"></textarea>
            </div>

            <div >
                <input wire:model="attachments" class="hidden task-input" type="file" name="attachments[]" id="attachments" placeholder="Choose files" multiple >
                <div id="attachments-list"></div>
            </div>
        </div>
        <div class="mr-0 mt-3">
            <div class="w-full flex justify-between">
                <div class="flex flex-row">
                    <div class="button btn-radius bg-green-light text-white cursor-pointer" wire:click="create_task">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        {{__('common.save')}}
                    </div>
                    <div class="button btn-radius bg-green-light text-white cursor-pointer" onClick="uploadAttachments()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        {{__('task.attach_file')}}
                    </div>
                </div>
                <div class="flex justify-end">
                    <div class="button btn-radius bg-green-light text-white !mr-0 cursor-pointer" wire:click="$emit('closeModal')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{__('common.cancel')}}
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        //Initilaize CK editor on swal open
        CKEDITOR.replace('editor', {
                uiColor: 'rgb(89,198,198)',
                height: '300px'
            }
        );

        CKEDITOR.instances["editor"].on('change', function() {
            @this.set('task.description', CKEDITOR.instances["editor"].getData());
        });

        //Initilaize datepicker on swal open
        $('#deadline').datepicker({
            format: 'yyyy-mm-dd',
            weekStart: 1,
            daysOfWeekDisabled: [0,6],
            startDate: '0d'
        }).on('changeDate', function(e){
            $('.datepicker').hide();
        });

        $("#deadline").change(function(event) {
            @this.set('task.deadline', event.target.value);
        });
    </script>
</div>
