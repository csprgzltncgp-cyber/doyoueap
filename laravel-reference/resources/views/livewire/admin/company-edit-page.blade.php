@section('title')
    Admin Dashboard
@endsection

@push('livewire_js')
    <script src="/assets/js/chosen.js" type="text/javascript" charset="utf-8"></script>
    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            @this.
            on('companyEditRendered', function () {
                $('#countries').chosen("destroy").chosen();
                $('.chosen-container').addClass('col-12 p-0')
            });

            $('#countries').chosen().change(function (e) {
                @this.
                set('countries', $(e.target).val());
            });
        })

        $('#save_company_button').on('click', function () {
            Swal.fire(
                'Sikeres mentés!',
                '',
                'success'
            );
        });
    </script>
@endpush

<div class="d-flex flex-column">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link href="/assets/css/chosen.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/bordered-checkbox.css')}}?v={{time()}}">

    <style>
        option:checked {
            background: rgba(89, 198, 198, 0.5) !important;
        }

        select[multiple]:focus option:checked {
            background: rgba(89, 198, 198, 0.5) !important;
        }

        select:not(:focus) option:checked {
            background: rgba(89, 198, 198, 0.5) !important;
        }
    </style>
    {{ Breadcrumbs::render('companies.edit', $company) }}

    <h1>{{$company->name}}</h1>

    <form wire:submit.prevent="saveCompanyData">
        <div class="form-group">
            <label for="name">{{__('workshop.company_name')}}</label>
            <input type="text" id="name" wire:model="name" placeholder="{{__('workshop.company_name')}}" required>
        </div>

        <div class="form-group">
            <label for="countries">{{__('common.countries')}}</label>
            <select wire:model="countries" id="countries" multiple class="chosen-select"
                    data-placeholder="{{__('common.countries')}}">
                @foreach($all_country as $country)
                    <option @if(in_array($country->id,$countries)) selected @endif
                    value="{{$country->id}}">{{$country->code}}</option>
                @endforeach
            </select>
        </div>

        @foreach($countries as $country)
            <livewire:admin.company-country-component
                    :country_id="$country"
                    :company="$company"
                    :wire:key="$country"
            />
        @endforeach

        <div class="form-group">
            <label for="active">{{__('crisis.active')}}</label>
            <div class="position-relative">
                <select id="active" wire:model="active" required autofocus="autofocus">
                    <option class="mb-2" value="1">{{__('common.yes')}}</option>
                    <option class="mb-2" value="0">{{__('common.no')}}</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <button id="save_company_button" type="submit" name="button">
                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                <span class="mt-1">{{__('common.save')}}</span>
            </button>
        </div>
    </form>
</div>
