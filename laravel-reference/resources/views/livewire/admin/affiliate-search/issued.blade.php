@push('livewire_js')
<script src="{{asset('assets/js/task/index.js')}}?v={{time()}}"></script>
<script>
    $('#orderBySelect').on('change', function(val) {
        var orderBy = $(this).val();
        @this.orderBy = orderBy;
    });

    $('#orderBySelect option[value="{{implode(',', $this->orderBy)}}"]').attr("selected",true);

    function deleteAffiliateSearch(id){
        Swal.fire({
            title: '{{__('common.are-you-sure-to-delete')}}',
            text: "{{__('common.operation-cannot-undone')}}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{__('common.yes-delete-it')}}',
            cancelButtonText: '{{__('common.cancel')}}',
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'DELETE',
                    data:{
                        id: id
                    },
                    url: '/ajax/delete-affiliate-search/' + id,
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        });
    }
</script>
@endpush

<div class="row">
    <link rel="stylesheet" href="{{asset('assets/css/cases/list_in_progress.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/tasks.css?v=') . time()}}">
    <style>
        select{
            width: 204px;
            display: block;
            appearance:none !important;
            -moz-appearance:none !important;
            -webkit-appearance:none !important;
            border:2px solid rgb(89,198,198) !important;
            padding:10px 0px 10px 15px!important;
            margin-bottom:20px !important;
            border-radius: 0px !important;
            outline: none !important;
            color:rgb(89,198,198) !important;
        }
    </style>

    <div class="col-12">
        {{Breadcrumbs::render('affiliate-search-workflow.issued')}}
        <h1>{{__('affiliate-search-workflow.menu')}}</h1>

        <x-affiliate-search.menu/>

        <div class="mt-5 mb-1">
            <x-tasks.sort/>
        </div>

        @forelse($affiliateSearches as $affiliateSearch)
            <x-affiliate-search :affiliateSearch="$affiliateSearch" :showToUser="true" :showDelete="true"/>
        @empty
            <p>{{__('affiliate-search-workflow.no_issued_workflows')}}!</p>
        @endforelse

        @if($affiliateSearches->hasMorePages())
            <div class="d-flex justify-content-center mb-5" style="margin-bottom: -25px; margin-top:25px;">
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
</div>
