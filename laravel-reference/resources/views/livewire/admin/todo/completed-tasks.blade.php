@push('livewire_js')
    <script>
        $('#orderBySelectCompleted').on('change', function(val) {
            var orderBy = $(this).val();
            @this.orderBy = orderBy;
        });

        $('#orderBySelectCompleted option[value="{{implode(',', $this->orderBy)}}"]').attr("selected",true);
    </script>
@endpush

<div>
    @if($tasks->count())

    <div class="headline-container">
        <h1 class="headline-black" style="background:rgba(89, 198, 198, 0.1);">
            {{__('task.completed')}}: {{$tasks->total()}}
        </h1>
        <x-tasks.sort id="orderBySelectCompleted"/>
    </div>
    <div class="card" style="background: rgba(89, 198, 198, 0.1);">
            <div style="margin-bottom: -15px;">
                @foreach($tasks as $task)
                    <x-tasks :task="$task" lineColor='rgba(255, 255, 255, 0.7)'/>
                @endforeach
            </div>

            @if($tasks->hasMorePages())
                <div class="d-flex justify-content-center" style="margin-bottom: -25px; margin-top:25px;">
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
    @endif
</div>
