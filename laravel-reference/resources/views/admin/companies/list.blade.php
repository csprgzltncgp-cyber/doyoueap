@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/invoicing.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/invoicing.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/cgp-card.css') }}">
    <style>
        .list-element button, .list-element a {
            margin-right: 10px;
            display: inline-block;
        }

        .list-element button.delete-button {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            border: 0 solid black;
            color: #007bff;
            outline: none;
        }

        .list-element {
            cursor: pointer;;
        }
    </style>
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/toggleActive.js')}}?v={{time()}}" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script>
        function deleteCompany(id, element) {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.yes-delete-it')}}',
                cancelButtonText: '{{__('common.cancel')}}',
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: '/ajax/delete-company/' + id,
                        success: function (data) {
                            if (data.status == 0) {
                                $(element).closest('.list-element').remove();
                            }
                        }
                    });
                }
            });

        }
    </script>
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('content')
    @livewire('admin.company.index')
@endsection
