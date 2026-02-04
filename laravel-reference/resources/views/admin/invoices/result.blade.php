@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script>
        const are_you_sure_you_want_to_delete_your_invoice = '{{__("common.are-you-sure-you-want-to-delete-your-invoice")}}';
        const operation_isnt_reversible = '{{__("common.operation-is-not-reversible")}}';
        const yes_delete_it = '{{__("common.yes-delete-it")}}';
        const cancel = '{{__("common.cancel")}}';
        const deletion_was_unsuccessful = '{{__("common.deletion-was-unsuccessful")}}';
        const error = '{{__("common.error")}}';
        const editing_was_unsuccessful = '{{__("common.editing-your-invoice-was-unsuccessful")}}';
        const are_you_sure_you_want_to_delete_caseid = '{{__("common.are-you-sure-to-delete-caseid")}}';
        const deleting_your_case_id_was_unsuccessful = '{{__("common.deleting-your-case-id-was-unsuccessfu")}}';
        const system_message = '{{__("common.system-message")}}';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="{{asset('assets/js/invoice/master.js')}}" charset="utf-8"></script>
    <script src="{{asset('assets/js/invoice/invoice_admin.js')}}?v={{time()}}" charset="utf-8"></script>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/invoices.css?v={{time()}}">
@endsection

@section('content')
    <div class="row m-0 w-100">
        {{ Breadcrumbs::render('invoices.filtered') }}

        <h1 class="col-12 pl-0">{{__('common.filter-results')}}</h1>
        <ul id="invoice-submenus" class="w-100">
            <li><a class="col-12 pl-0 d-block add-new-invoice btn-radius" href="{{route('admin.invoices.filter')}}"><svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; margin-bottom:3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>{{__('crisis.apply_filter')}}</a></li>
            <li><a class="col-12 pl-0 d-block add-new-invoice btn-radius" href="{{route('admin.invoices.index')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    {{__('common.list-of-invoices')}}</a></li>
        </ul>
        @if(!$invoices->count())
            <p>{{__('crisis.no_filter_result')}}</p>
        @endif
        @foreach($invoices as $invoice)
            @component('components.invoices.admin_invoice_component',
            [
              'invoice' => $invoice,
              'event' => $invoice->last_event()
            ])@endcomponent
        @endforeach
    </div>
@endsection
