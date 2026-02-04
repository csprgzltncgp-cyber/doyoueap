@section('title')
    Admin Dashboard
@endsection

@push('livewire_js')
    <script>
        Livewire.on('companyInputsSaved', function () {
          showSuccessAlert();
        });

        @if(session()->has('companyInputValuesSaved'))
                showSuccessAlert();
        @endif

        function showSuccessAlert(){
            Swal.fire(
                '{{__('common.case_input_edit.successful_save')}}',
                '',
                'success'
            );
        }
    </script>
@endpush

<div class="row m-0">
    <link rel="stylesheet" href="/assets/css/form.css?v={){time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/bordered-checkbox.css')}}?v={{time()}}">

    <style>
        form input, form select, form select options, form textarea, form ul {
            border: 2px solid rgb(0, 87, 95) !important;
            color: rgb(0, 87, 95) !important;
        }

        form input::placeholder {
            color: rgb(0, 87, 95) !important;
        }
    </style>

    {{ Breadcrumbs::render('companies.input-edit', $company) }}

    <h1 class="col-12 pl-0">{{$company->name}} {{__('common.edit-of-inputs')}}</h1>

    <form wire:submit.prevent="save" class="row w-100 mt-3" style="max-width: inherit">
        @foreach($inputs as $input)
            <livewire:admin.case-input-component
                    :company="$company"
                    :case-input="$input"
                    :wire:key="$input->id"
            />
        @endforeach
        <div class="col-12 pl-0 mb-5">
            <div class="col-5">
                <button wire:click="addNew" type="button"
                style="margin-bottom:10px; --btn-max-width: var(--btn-min-width); --btn-margin-bottom: var(--btn-margin-y"
                        class="col-4 btn-radius">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width: 20px;"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{__('common.add')}}
                </button>
            </div>
            <div class="col-5">
                <button type="submit" class="col-4 btn-radius" style="--btn-max-width: var(--btn-min-width)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px;"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    {{__('common.save')}}
                </button>
            </div>
        </div>
    </form>
</div>
