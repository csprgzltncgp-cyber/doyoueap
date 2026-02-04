@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">

    <style>
        .list-elem {
            background: rgb(222, 240, 241);
            color: black;
            text-transform: uppercase;
            margin-right: 10px;
            min-width: 200px;
        }

        .list-elem:hover {
            color: black;
        }

        .list-element button, .list-element a {
            margin-right: 10px;
            display: inline-block;
        }

        .list-element button.delete-button {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            border: 0px solid black;
            color: #007bff;
            outline: none;
        }

        .list-element {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

    </style>
@endsection

@section('extra_js')
<script>
    function delete_place(id, element) {
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
                    type: 'POST',
                    url: '/ajax/delete-onsite-consultation-place/' + id,
                    success: function (data) {
                        $('#place_row_'+id).remove();
                    }
                });
            }
        });
    }
</script>
@endsection

@section('content')
    <div class="row">
        <div class="row col-12">
            <div class="col-12">
                {{ Breadcrumbs::render('eap-online.onsite-consultation.place.index') }}
                <h1>{{ __('eap-online.onsite_consultation.places') }}</h1>

                <div class="d-flex flex-column mb-3">
                    <a href="{{route('admin.eap-online.onsite-consultation.place.create')}}">{{ __('eap-online.onsite_consultation.new_place') }}</a>
                </div>

                <div>
                    @foreach($places as $place)
                        <div class="list-element col-12 group" id="place_row_{{$place->id}}">
                            <div class="d-flex align-items-center">
                                <p class="mr-3">
                                    {{$place->name}} -
                                    {{$place->address}}
                                </p>
                            </div>

                            <div>
                                @if ($place->consultations->isEmpty())
                                    <button class="float-right delete-button" onclick="delete_place({{$place->id}})">
                                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" 
                                            style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" 
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        {{__('common.delete')}}
                                    </button>
                                @endif
    
                                <a href="{{ route('admin.eap-online.onsite-consultation.place.edit', $place) }}">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                        style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    {{__('common.edit')}}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
