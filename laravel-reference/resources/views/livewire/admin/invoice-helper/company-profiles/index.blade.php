@push('livewire_js')
<script>
    document.addEventListener('livewire:load', function () {
        window.livewire.hook('message.processed', (el, component) => {
            let companies = @this.js_companies.map(function(company){
                return company.id;
            });

            companies.forEach(function(companyId){
                const parent = $(`#countries-${companyId}`).parents('[wire\\:id]').first().attr('wire:id');
                const livewireComponent = window.livewire.find(parent);

                $(`#countries-${companyId}`).chosen().change(function (e) {
                    livewireComponent.set('countries', $(e.target).val());
                });

                $(`#contract_start-${companyId}`).datepicker({
                    format: 'yyyy-mm-dd',
                }).change(function (event) {
                    livewireComponent.set('contractDate', event.target.value);
                });

                $(`#contract_end-${companyId}`).datepicker({
                    format: 'yyyy-mm-dd',
                }).change(function (event) {
                    livewireComponent.set('contractDateEnd', event.target.value);
                });
            });
        });
    });
</script>
@endpush
<div>
    <div class="list-element-div" style="display: block">
        <x-invoice-helper.search />

        @foreach($companies as $company)

            <div class="invoice-list-holder" >
                <div class="case-list-in col-12 group" wire:click='toggleOpenCompany({{$company->id}})'>
                    @if(has_company_missing_information($company))
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px; color:#f70000; margin-right:5px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    @endif
                    <span>{{$company->name}}</span>
                    <button class="caret-left float-right">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            style="width: 20px; height: 20px; @if(in_array($company->id, $opened_companies)) transform: rotateZ(180deg); @endif" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>

                <div class="invoice-list @if(!in_array($company->id, $opened_companies)) d-none @endif mb-5">
                    @livewire('admin.company.edit', ['company' => $company, 'disabled_breadcrumb' => true], key('company-' . $company->id))
                </div>
            </div>
        @endforeach

        @if($companies->hasMorePages())
            <div class="d-flex">
                <div wire:loading.remove class="load-more-container btn-radius">
                    <div class="green-box button-c" style="width: 208px;" wire:click="loadMore">
                        {{__('invoice-helper.load-more')}}
                    </div>

                    <div class="green-box button-c" style="width: 208px;" wire:click="loadAll">
                        {{__('invoice-helper.load-all')}}
                    </div>
                </div>

                <img wire:loading src="{{asset('assets/img/spinner.svg')}}" alt="spinner">
            </div>
        @endif
    </div>
</div>
