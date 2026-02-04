@push('livewire_js')
    <script>
        $('#orderBySelectToday').on('change', function(val) {
            var orderBy = $(this).val();
            @this.orderBy = orderBy;
        });

        $('#orderBySelectToday option[value="{{implode(',', $this->orderBy)}}"]').attr("selected",true);
    </script>
@endpush

<div>
    @if($affiliateSearches->count())
        <div class="headline-container">
            <h1 class="headline" style="background:#59c6c6;">
                {{__('task.today')}}: {{$affiliateSearches->count()}}
            </h1>

            <x-tasks.sort id="orderBySelectToday"/>
        </div>
        <div class="card" style="background: #59c6c6;">
            <div style="margin-bottom: -15px;">
                @foreach($affiliateSearches as $affiliateSearch)
                    <x-affiliate-search :affiliateSearch="$affiliateSearch" lineColor='rgba(255, 255, 255, 0.7)'/>
                @endforeach
            </div>

            @if($affiliateSearches->hasMorePages())
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
