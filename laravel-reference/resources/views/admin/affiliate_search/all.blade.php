@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/task/index.js')}}?v={{time()}}"></script>
    <script>
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
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list_in_progress.css')}}?t={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/tasks.css?v=').time()}}">
    <style>
        .rotated-icon{
            transform: rotate(180deg);
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{Breadcrumbs::render('affiliate-search-workflow.all')}}
            <h1>{{__('affiliate-search-workflow.menu')}}</h1>

            <x-affiliate-search.menu/>

            <div class="mb-5"></div>

            @foreach($admins as $admin)
                <div class="list-element case-list-in" onclick="openList(this, '{{$admin->id}}')">
                    @if($affiliateSearches->where('to_id', $admin->id)->map(function ($affiliateSearch){ return $affiliateSearch->is_over_deadline();})->sum())
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; color: rgb(219, 11, 32); margin-bottom: 2px;" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    @endif
                    {{$admin->name}}
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px" class="float-right arrow"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div class="task-list d-none" id="admin_{{$admin->id}}">
                    @foreach($affiliateSearches->where('to_id', $admin->id) as $affiliateSearch)
                        <x-affiliate-search :affiliateSearch="$affiliateSearch" :showDelete="true"/>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endsection
