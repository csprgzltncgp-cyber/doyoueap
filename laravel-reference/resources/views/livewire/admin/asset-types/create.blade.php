@push('livewire_js')
    <script src="/assets/js/chosen.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ asset('assets/js/datetime.js') }}"></script>
    <script>
        $("#countries").chosen().change(function(e) {
            @this.set('countries', $(e.target).val());
        });

        $("#contract_start").datepicker({
            format: 'yyyy-mm-dd',
        });

        $("#contract_end").datepicker({
            format: 'yyyy-mm-dd',
        });

        $("#contract_start").change(function(event) {
            @this.set('contractDate', event.target.value);
        });

        $("#contract_end").change(function(event) {
            @this.set('contractDateEnd', event.target.value);
        });

        Livewire.on('successEvent', function(success) {
            Swal.fire({
                title: success,
                text: '',
                icon: 'success',
                confirmButtonText: 'Ok'
            });
        });

        async function setNewPassword() {
            const {
                value: formValues
            } = await Swal.fire({
                title: '{{ __('assetedit.set-new-password') }}',
                html: `
                    <label style="float: left; font-size:15px; margin-top:15px" for="new-password">{{ __('operator-data.new_password') }}</label>
                    <div data-content="">
                        <input style="margin-top:0" id="new-password" class="swal2-input" type="password" required>
                    <div>

                    <label style="float: left; font-size:15px; margin-top:15px" for="password-confirmation">{{ __('common.force-change-password.password-confirmation') }}</label>
                    <div data-content="">
                        <input style="margin-top:0" id="password-confirmation" class="swal2-input" type="password" required>
                    <div>
                    `,
                focusConfirm: false,
                preConfirm: () => {
                    return {
                        password: document.getElementById('new-password').value,
                        password_confirmation: document.getElementById('password-confirmation').value,
                    }
                }
            });

            if (formValues) {

                if (formValues.password != formValues.password_confirmation || (formValues.password == '' && formValues
                        .password_confirmation == '')) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('common.password-incorrect') }}',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                @this.set('clientUser.password', formValues.password);

                Swal.fire({
                    title: '{{ __('common.new-password-success') }}',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }
    </script>
@endpush

<div>
    <link rel="stylesheet" href="/assets/css/form.css?v={{ time() }}">
    <link rel="stylesheet" href="/assets/css/asset/asset.css?v={{ time() }}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bordered-checkbox.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cases/datetime.css') }}">

    <style>
        .chosen-container {
            width: auto !important;
            flex: 1 0 auto !important;
        }

        .form-group input {
            color: black !important;
        }

        .form-group select {
            color: black !important;
        }

        .chosen-container.chosen-container-multi {
            width: min-content !important;
        }
    </style>

    {{ Breadcrumbs::render('assets.create-type') }}

    <h1>{{ __('asset.types') }}</h1>

    <div class="row m-0">
        <form wire:submit.prevent="store" style="max-width: 1500px !important;" autocomplete="off" novalidate>

            <div class="mt-5">
                <div class="form-row">
                    @foreach ($types as $type)
                        <div class="form-group col-md-0 mb-0">
                            <div class="d-flex flex-column">
                                <div class="input-group col-12 p-0">
                                    <div class="input-group-prepend">
                                        <div class="eq-data">
                                            <span class="eq-title mr-3">{{ $type->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($addNew)
                <div class="form-row">
                    <div class="form-group col-md-3 mb-0">
                        <div class="d-flex flex-column" style="min-width:200px;">
                            <input type="text" wire:model="name" placeholder="Eszköz típus elnevezése" required>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-12 p-0 mt-3">
                <div class="d-flex align-items-center" style="max-width: 45%">
                    <button type="button" style="text-transform: uppercase;"
                        wire:click="addType()" class="text-center btn-radius">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height:20px;"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="mt-1">
                            {{ __('common.add') }}
                        </span>
                    </button>
                </div>
            </div>

            <div class="col-12 p-0">
                <div class="d-flex align-items-center" style="max-width: 45%">
                    <button type="submit" style="padding-bottom: 14px; padding-left:5px; text-transform: uppercase;"
                        name="button" class="text-center btn-radius">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width:20px;"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                            </path>
                        </svg>
                        <span class="mt-1">
                            {{ __('common.save') }}
                        </span>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
