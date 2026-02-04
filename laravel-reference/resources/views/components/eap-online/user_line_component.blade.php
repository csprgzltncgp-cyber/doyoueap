@section('extra_js')
    <script>
        function deleteUser(userId) {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.delete')}}',
                cancelButtonText: '{{__('common.cancel')}}',
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: `/ajax/eap-user-delete/${userId}`,
                        success: function () {
                            location.reload();
                        }
                    });
                }
            });
        }
    </script>
@endsection

<style>
    #new-password {
        background: rgb(0, 87, 95);
        color: white;
        text-transform: uppercase;
        border: none;
    }

    #new-password:hover {
        text-decoration: underline !important;
    }
</style>

<div class="list-element mt-3">
    <span class="data mr-0">
        @if($user && $user->company)
            {{$user->username}}
            - {{$user->company->name}}
            - {{$user->country->code}} 
            - {{$user->email}} 
            - {{__('eap-online.users.email_verified_at')}}: @if($user->email_verified_at) {{$user->email_verified_at->format('Y-m-d H:i')}} @else {{__('eap-online.users.email_unverified')}} @endif
        @endif
    </span>
    @if(Auth::user()->type == 'admin')
        <button id="new-password" class="btn-radius justify-content-center align-items-center" style="cursor: pointer; color: white; --btn-margin-left: var(--btn-margin-x)" id="newPassword"
                onclick="deleteUser({{$user->id}})">
                <svg class="fuction-btn"
                    xmlns="http://www.w3.org/2000/svg"
                    style="width: 20px; height:20px; margin-bottom: 3px"
                    fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            <span>{{__('common.delete')}}</span>
        </button>
    @endif
</div>
