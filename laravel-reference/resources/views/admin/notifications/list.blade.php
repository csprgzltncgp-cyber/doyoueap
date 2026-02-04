@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script>
        function deleteNotification(id, element) {
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
                        url: '/ajax/delete-notification/' + id,
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

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v=1597781294">
    <style>
        .notification-list-item {
            margin-bottom: 0px;
            display: inline-block;
            max-width: 70%;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        button.delete-notification {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearanne: none;
            background: transparent;
            border: 0px solid transparent;
            outline: none !important;
            color: #007bff;
        }

        .list-element {
            justify-content: space-between;
            display: flex;
        }
    </style>
@endsection

@section('content')
    <div class="row m-0">
        {{ Breadcrumbs::render('notifications') }}
        <h1 class="col-12 pl-0">{{__('common.notifications')}}</h1>
        <a href="{{route('admin.notifications.new')}}" class="col-12 pl-0 d-block">Új értesítés hozzáadása</a>
        @foreach($notifications as $notification)
            <div class="list-element col-12">
                <p class="notification-list-item">{{$notification->display_from}}
                    - {{$notification->translation ? $notification->translation->value : ($notification->allTranslations && $notification->allTranslations->first() ? $notification->allTranslations->first()->value :  '' )}}</p>
                <div>
      <span class="mr-2"><svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                              style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                              stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
</svg> {{$notification->seen->count()}}</span>
                    <a href="{{route('admin.notifications.edit',['id' => $notification->id])}}">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Szerkesztés</a>
                    <button class="delete-notification" onClick="deleteNotification({{$notification->id}}, this)">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Törlés
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endsection
