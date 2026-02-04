@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
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
            border: 0px solid black;
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
        function deleteDocument(id, element) {
            Swal.fire({
                title: 'Biztos, hogy törölni szeretnéd?',
                text: "A művelet nem visszavonható!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Igen, törlöm!',
                cancelButtonText: 'Mégsem',
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: '/ajax/delete-document/' + id,
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
    <div class="row m-0">
        {{ Breadcrumbs::render('documents') }}
        <h1 class="col-12 pl-0">{{__('common.list_of_documents')}}</h1>
        <a href="{{route('admin.documents.new')}}" class="col-12 d-block pl-0">Új menüpont hozzáadása</a>
        @foreach($countries as $country)
            <div class="list-element case-list-in mb-0 col-12 group" onClick="toggleList({{$country->id}}, this, event)">
                {{$country->code}}
                <button class="caret-left float-right">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
            @foreach($documents->where('country_id',$country->id) as $doc)
                <div class="list-element col-12 d-none" data-country="{{$country->id}}">
                    <span>{{$doc->name}}</span>
                    -
                    <span>{{optional(optional($doc)->language)->name}}</span>
                    -
                    <span>{{$doc->visibility}}</span>
                    <button class="float-right delete-button" onClick="deleteDocument({{$doc->id}}, this)">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Törlés
                    </button>
                    <a class="float-right" href="{{route('admin.documents.edit',['id' => $doc->id])}}">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Szerkesztés</a>
                </div>
            @endforeach
        @endforeach
    </div>
@endsection
