@push('livewire_js')
    <script>
        $('#orderBySelectThisWeek').on('change', function(val) {
            var orderBy = $(this).val();
            @this.orderBy = orderBy;
        });

        $('#orderBySelectThisWeek option[value="{{implode(',', $this->orderBy)}}"]').attr("selected",true);
    </script>
@endpush

<div>
    @if($tasks->count())
    <div class="headline-container">
        <h1 class="headline-black" style="background:rgba(89, 198, 198, 0.4);">
            {{__('task.this_week')}}: {{$tasks->total()}}
        </h1>
        <x-tasks.sort id="orderBySelectThisWeek"/>
    </div>

    <div class="card" style="background: rgba(89, 198, 198, 0.4);">
            <div style="margin-bottom: -15px;">
                @foreach($tasks as $task)
                    <x-tasks :task="$task" lineColor='rgba(255, 255, 255, 0.7)' :only-show-days="true"/>
                @endforeach
            </div>

            @if($tasks->hasMorePages())
                <div class="d-flex justify-content-center" style="margin-bottom: -25px; margin-top:25px;">
                    <button class="button-link" wire:click.prevent='loadMore'>
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        {{__('common.load-more')}}
                    </button>
                    <button class="button-link" wire:click.prevent='loadAll'>
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                        </svg>
                        {{__('common.load-all')}}
                    </button>
                </div>
            @endif
        </div>
    @endif
</div>
