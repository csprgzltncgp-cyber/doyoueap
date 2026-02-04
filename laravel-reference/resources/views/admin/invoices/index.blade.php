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
        const deleting_your_case_id_was_unsuccessful = '{{__("common.deleting-your-case-id-was-unsuccessful")}}';
        const system_message = '{{__("common.system-message")}}';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"
            integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{asset('assets/js/invoice/master.js')}}?v={{time()}}" charset="utf-8"></script>
    <script src="{{asset('assets/js/invoice/invoice_admin.js')}}?v={{time()}}" charset="utf-8"></script>
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/invoices.css')}}?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"
          integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="{{asset('assets/css/datetimepicker.css')}}">
    <style>
        .lis-element-div {
            width: 100%;
            display: none;
        }

        .lis-element-div.active {
            display: block !important;
        }

        .lis-element-div-c {
            display: none;
        }

        .lis-element-div-c.active {
            display: block !important;
        }

        .list-element.col-12 {
            display: inline-block !important;
        }
    </style>

@endsection
@section('content')
    <div class="row m-0 w-100">
        {{ Breadcrumbs::render('invoices') }}

        <h1 class="col-12 pl-0">{{__('common.list-of-invoices')}}</h1>
        <a href="{{route('admin.invoices.filter')}}" id="filter" class="mb-4 btn-radius">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; margin-bottom:3px" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
            </svg>
            Új szűrés</a>
        @foreach($filtered_years as $filtered_year)
            <div class="case-list-in col-12 group" onclick="yearOpen({{$filtered_year}})">
                {{$filtered_year}}
                <button class="caret-left float-right">
                    <svg id="y{{$filtered_year}}" xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
            <div class="lis-element-div" id="{{$filtered_year}}">
                @foreach($filtered_months as $filtered_month)
                    @php $test_year = substr($filtered_month,0,-3); @endphp
                    @if($test_year == $filtered_year)
                        @php $month_id = str_replace("-","_", $filtered_month); @endphp
                    <div class="invoice-list-holder">
                        <div class="case-list-in col-12 group" onclick="monthOpen('{{$month_id}}')">
                            {{$filtered_month}}
                            <button class="caret-left float-right">
                                <svg id="m{{$month_id}}" xmlns="http://www.w3.org/2000/svg"
                                     style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                        <div class="invoice-list d-none" id="{{$month_id}}">
                        </div>
                        <img id="m_{{$month_id}}" class="d-none spinner" src="{{asset('assets/img/spinner.svg')}}"
                             alt="spinner">
                        <div class="d-flex justify-content-center">
                            <button class="load-more-cases btn-radius d-none" id="m_{{$month_id}}"
                                    onclick="loadMore('{{$month_id}}', this, false)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                     style="width: 20px; height: 20px"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                                {{__('common.load-more')}}</button>
                            <button class="load-all-cases load-more-cases btn-radius d-none" id="m_{{$month_id}}"
                                    onclick="loadMore('{{$month_id}}', this, true)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                     style="width: 20px; height: 20px"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19 13l-7 7-7-7m14-8l-7 7-7-7"/>
                                </svg>
                                {{__('common.load-all')}}</button>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        @endforeach
    </div>
@endsection
