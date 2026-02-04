@push('livewire_js')
    <script>
        $('#orderByUserLine{{$user->id}}').on('change', function(val) {
            var orderBy = $(this).val();
            @this.orderBy = orderBy;
        });

        $('#orderByUserLine{{$user->id}} option[value="{{implode(',', $this->orderBy)}}"]').attr("selected",true);
    </script>
@endpush

<div>
    <div class="list-element case-list-in" wire:click='toggleOpen'>
        @if($has_over_deadline_tasks)
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; color: rgb(219, 11, 32); margin-bottom: 2px;" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        @endif
        {{$user->name}}
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="float-right arrow"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
    <div class="task-list @if(!$isOpened) d-none @endif" id="admin_{{$user->id}}">
        <div class="mb-2 mt-3">
            <x-tasks.sort id="orderByUserLine{{$user->id}}"/>
        </div>
        @foreach($tasks as $task)
            <x-tasks :task="$task" :showDelete="true"/>
        @endforeach

        @if($tasks->hasMorePages())
        <div class="d-flex justify-content-center" style="margin-bottom: 25px; margin-top:25px;">
            <button class="load-more-cases btn-radius" wire:click.prevent='loadMore'>
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                </svg>
                {{__('common.load-more')}}
            </button>
            <button class="load-more-cases btn-radius" wire:click.prevent='loadAll'>
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                </svg>
                {{__('common.load-all')}}
            </button>
        </div>
    @endif
    </div>
</div>
