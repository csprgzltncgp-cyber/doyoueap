@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/task/index.js')}}?v={{time()}}"></script>
    <script>
        function deleteTask(id){
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
                        url: '/ajax/delete-task/' + id,
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
            {{ Breadcrumbs::render('todo.index') }}
            <h1>TODO</h1>

            <x-tasks.menu/>

            <div class="mb-5"></div>

            @foreach($admins as $admin)
                @livewire('admin.todo.user-tasks-line', ['user' => $admin])
            @endforeach
        </div>
    </div>
@endsection
