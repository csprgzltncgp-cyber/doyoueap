<style>
    .hourly_rate_50{
        display: initial;
        position: relative;
    }

    .hourly_rate_50::after{
        position: absolute;
        top: -2px;
        right: .5em;
    }

    .hourly_rate_30{
        display: initial;
        position: relative;
    }

    .hourly_rate_30::after{
        position: absolute;
        top: -2px;
        right: .5em;
    }

    .hourly_rate_30::after {
        content: attr(data-content);
    }

    .hourly_rate_50::after {
        content: attr(data-content);
    }

    .swal2-popup{
        padding: 3.5em !important;
    }

</style>


<script>
    function showOutOfDateAlert(){
        Swal.fire(
            '{{__('common.invoice-creation-date-exceeded')}}',
            '',
            'warning'
        );
    }

    async function showCurrencyPopup(){
        const { value: formValues } = await Swal.fire({
            title: '{{__('invoice.currency_title')}}',
            html:
                `
                <label style="float: left; font-size:15px; margin-top:15px" for="currency">{{__('invoice.currency')}}</label>
                <select style="margin-top:0" id="currency" class="swal2-input" required name="currency">
                    <option disabled selected>{{__('common.please-choose')}}</option>
                    <option value="chf">CHF</option>
                    <option value="czk">CZK</option>
                    <option value="eur">EUR</option>
                    <option value="huf">HUF</option>
                    <option value="mdl">MDL</option>
                    <option value="oal">OAL</option>
                    <option value="pln">PLN</option>
                    <option value="ron">RON</option>
                    <option value="rsd">RSD</option>
                    <option value="usd">USD</option>
                </select>
                ` +
                `
                <label style="float: left; font-size:15px; margin-top:15px" for="currency">{{__('invoice.hourly_rate_50')}}</label>
                <div class="hourly_rate_50" data-content="">
                    <input style="margin-top:0" id="hourly_rate_50" class="swal2-input" placeholder="" disabled>
                <div>
                `
                @if(auth()->user()->hasPermission(2) || auth()->user()->hasPermission(3) || auth()->user()->hasPermission(7))
                    +`
                    <label style="float: left; font-size:15px; margin-top:15px" for="currency">{{__('invoice.hourly_rate_30')}}</label>
                    <div class="hourly_rate_30" data-content="">
                        <input style="margin-top:0" id="hourly_rate_30" class="swal2-input" placeholder="" disabled>
                    <div>
                    `
                @endif
                ,
            focusConfirm: false,
            preConfirm: () => {
                return {
                    currency: document.getElementById('currency').value,
                    hourly_rate_50: document.getElementById('hourly_rate_50').value.replace(/\s/g, ''),
                    @if(auth()->user()->hasPermission(2) || auth()->user()->hasPermission(3) || auth()->user()->hasPermission(7))
                        hourly_rate_30: document.getElementById('hourly_rate_30').value.replace(/\s/g, ''),
                    @endif
                }
            }
        });

        if (formValues) {
            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/ajax/save-hourly-rate',
                    data: formValues,
                    success: function (data) {
                        if(data.status == 0){
                            location.replace("{{route('expert.invoices.new')}}");
                        }
                    },
                    error: function (error) {
                        Swal.fire(
                            '{{__('common.error-occured')}}',
                            '',
                            'error'
                        );
                    }
                });
        }
    }

    function addCurrencyPopupEventListeners(){
        document.getElementById('currency').addEventListener('change', function(){
            document.getElementById('hourly_rate_50').disabled = false;
            document.querySelector('.hourly_rate_50').setAttribute('data-content', this.value.toUpperCase() + '/50 ' + '{{__('crisis.minute')}}');
            document.getElementById('hourly_rate_50').placeholder = this.value.toUpperCase() + '/50 ' + '{{__('crisis.minute')}}';
            @if(auth()->user()->hasPermission(2) || auth()->user()->hasPermission(3) || auth()->user()->hasPermission(7))
                document.getElementById('hourly_rate_30').disabled = false;
                document.querySelector('.hourly_rate_30').setAttribute('data-content', this.value.toUpperCase() + '/30 ' + '{{__('crisis.minute')}}');
                document.getElementById('hourly_rate_30').placeholder = this.value.toUpperCase()  + '/30 ' + '{{__('crisis.minute')}}';
            @endif
        });

        document.getElementById('hourly_rate_50').addEventListener('keyup', function(){
            let current_value = this.value.replace(/\D/g,'');
            this.value = current_value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        });

        @if(auth()->user()->hasPermission(2) || auth()->user()->hasPermission(3) || auth()->user()->hasPermission(7))
            document.getElementById('hourly_rate_30').addEventListener('keyup', function(){
                let current_value = this.value.replace(/\D/g,'');
                this.value = current_value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
            });
        @endif
    }

    function showInvoiceAlreadyCreated(){
        Swal.fire(
            '{{__('common.invoice-already-created-1')}}',
            '{{__('common.invoice-already-created-alt')}}',
            'warning'
        );
    }

    function showCurrencyChangePopup() {
        Swal.fire(
            '{{__('currency-change.notification-popup-title')}}',
            '{{__('currency-change.notification-popup-body')}}',
            'warning'
        ).then(function(){
            location.replace("{{route('expert.currency-change.index')}}");
        });
    }
</script>


<ul id="invoice-submenus" class="row ml-0">
    <li class="m-0">
        <a class="col-12 pl-0 d-block add-new-invoice btn-radius" href="{{route('expert.invoices.index')}}">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            {{__('common.list-of-invoices')}}
        </a>
    </li>

    <li class="m-0">
        <a class="col-12 pl-0 d-block add-new-invoice btn-radius"
            @if((date('d') >= 1 && date('d') <= 10) || has_invoicing_opened(auth()->user()))
                @if(auth()->user()->invoices()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->get()->count() > 0)
                    href="javascript:void(0)"
                    onclick="showInvoiceAlreadyCreated()"
                @elseif(empty(auth()->user()->invoice_datas()->first()->currency)
                || empty(auth()->user()->invoice_datas()->first()->hourly_rate_50) && !in_array(auth()->user()->invoice_datas()->first()->invoicing_type, [\App\Enums\InvoicingType::TYPE_CUSTOM, \App\Enums\InvoicingType::TYPE_FIXED])
                || (empty(auth()->user()->invoice_datas()->first()->hourly_rate_30) && !in_array(auth()->user()->invoice_datas()->first()->invoicing_type, [\App\Enums\InvoicingType::TYPE_CUSTOM, \App\Enums\InvoicingType::TYPE_FIXED]) && (auth()->user()->hasPermission(2) || auth()->user()->hasPermission(3) || auth()->user()->hasPermission(7))))
                    href="javascript:void(0)"
                    onclick="showCurrencyPopup(); addCurrencyPopupEventListeners();"
                @elseif(currency_change_documnet_missing() && auth()->user()->invoice_datas()->first()->invoicing_type !== \App\Enums\InvoicingType::TYPE_CUSTOM)
                    href="javascript:void(0)"
                    onclick="showCurrencyChangePopup();"
                @else
                    href="{{route('expert.invoices.new')}}"
                @endif
            @else
                href="javascript:void(0)"
                onclick="showOutOfDateAlert()"
            @endif
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{__('common.add-new-invoice')}}
        </a>
    </li>

    <li>
        <a class="col-12 pl-0 d-block add-new-invoice btn-radius" href="{{route('expert.invoices.infos')}}">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{__('common.information-about-invoicing')}}
        </a>
    </li>
</ul>
