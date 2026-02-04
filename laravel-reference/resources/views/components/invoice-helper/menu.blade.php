@props([
    'contractHolderCompany' => null,
    'invoicing_years' => [],
    'selected_year' => null
])
<div>
    <script>
        window.addEventListener('load', function() {
            document.getElementById('contract_holder_select').addEventListener('change', function() {
                let selectedValue = this.value;
                let redirectUrl = '';

                if(selectedValue == 1){
                    redirectUrl = '{{route('admin.invoice-helper.direct-invoicing.index')}}';
                }else if(selectedValue == 2){
                    redirectUrl = '{{route('admin.invoice-helper.direct-invoicing.index', ['contract_holder_company' => App\Enums\ContractHolderCompany::LIFEWORKS->value])}}'+'&year='+year;
                }else if(selectedValue == 3){
                    redirectUrl = '{{route('admin.invoice-helper.direct-invoicing.index', ['contract_holder_company' =>  App\Enums\ContractHolderCompany::OPTUM->value])}}'+'&year='+year;
                }else if(selectedValue == 4){
                    redirectUrl = '{{route('admin.invoice-helper.direct-invoicing.index', ['contract_holder_company' =>  App\Enums\ContractHolderCompany::PULSO->value])}}'+'&year='+year;
                }else if(selectedValue == 5){
                    redirectUrl = '{{route('admin.invoice-helper.direct-invoicing.index', ['contract_holder_company' =>  App\Enums\ContractHolderCompany::COMPSYCH->value])}}'+'&year='+year;
                }

                location.replace(redirectUrl);
            });

            // Reload page after year select cahnge
            document.getElementById('year_select').addEventListener('change', function() {
                
                spinner.classList.add('d-flex');
                spinner.classList.remove('d-none');

                months_container.classList.add('d-none');

                let selectedValue = this.value;
                let redirectUrl = '';

                location.replace(redirectUrl+"?year="+selectedValue+"&contract_holder_company={{ $contractHolderCompany }}");
            });
        });
    </script>
    <div class="contract-holder-selector mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" style="heigth:20px; width:20px;" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
        </svg>
        <span class="mr-2">{{__('invoice-helper.selected')}}:</span>
        <select id="contract_holder_select">
            <option value="1" @if(is_null($contractHolderCompany)) selected @endif>CGP Europe</option>
            <option value="2" @if($contractHolderCompany == App\Enums\ContractHolderCompany::LIFEWORKS->value) selected @endif>Lifeworks</option>
            <option value="3" @if($contractHolderCompany == App\Enums\ContractHolderCompany::OPTUM->value) selected @endif>Optum</option>
            <option value="4" @if($contractHolderCompany == App\Enums\ContractHolderCompany::PULSO->value) selected @endif>Pulso</option>
            <option value="5" @if($contractHolderCompany == App\Enums\ContractHolderCompany::COMPSYCH->value) selected @endif>Compsych</option>
        </select>
        
        @if(!empty($invoicing_years))
            <span class="mr-2 ml-2">Év:</span>
            <select id="year_select">
                @foreach ($invoicing_years as $year)
                    <option @if($year->year == request()->year) selected @endif value="{{ $year->year }}">{{ $year->year }}</option>
                @endforeach
            </select>
        @endif
    </div>
    
    <div class="menu-point-selector mt-4">
        <a
            href="{{route('admin.invoice-helper.direct-invoicing.index', ['contract_holder_company' => $contractHolderCompany, 'year' => $selected_year])}}"
            class="menu-point @if(strpos(url()->current(), 'invoice-helper/direct-invoicing')) active @endif">
            {{__('invoice-helper.invoicing')}}
        </a>

        @if(is_null($contractHolderCompany))
            <a
                href="{{route('admin.invoice-helper.completion-certificate.companies', ['year' => $selected_year])}}"
                class="menu-point @if(strpos(url()->current(), 'invoice-helper/completion-certificate')) active @endif"
                style="margin-bottom: 0;"
            >
                {{__('invoice-helper.completion-certificate.menu')}}
            </a>

            <a
                href="{{route('admin.invoice-helper.envelope.companies', ['year' => $selected_year])}}"
                class="menu-point @if(strpos(url()->current(), 'invoice-helper/envelope')) active @endif"
                style="margin-bottom: 0;"
            >
                {{__('invoice-helper.envelope.menu')}}
            </a>
        @endif

        {{-- <a
            class="menu-point @if(strpos(url()->current(), 'invoice-helper/company-profiles')) active @endif"
            href="{{route('admin.invoice-helper.company-profiles.index')}}"
        >
            {{__('invoice-helper.company-profiles')}}
        </a> --}}

        @if(is_null($contractHolderCompany))
            <a class="menu-point @if(strpos(url()->current(), 'invoice-helper/cgp-data')) active @endif"
                href="{{route('admin.invoice-helper.cgp-data')}}"
            >
                {{__('invoice-helper.cgp-data.menu')}}
            </a>
        @else
            <a class="menu-point @if(strpos(url()->current(), 'invoice-helper/contract-holder-company-data')) active @endif"
                href="{{route('admin.invoice-helper.contract-holder-company-data', ['contract_holder_company' => $contractHolderCompany])}}"
            >
                {{__('invoice-helper.cgp-data.menu')}}
            </a>
        @endif
    </div>
</div>
