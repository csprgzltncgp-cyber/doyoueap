@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/filter.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
@endsection

@section('extra_js')
    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script>
        $(function () {
            $('.datepicker').datepicker({
                'format': 'yyyy-mm-dd'
            });
            arrowClick();
        });

        function arrowClick() {
            $('.filter-button').click(function () {
                var options = $(this).closest('.filter').find('.options');
                options.toggleClass('d-none');
                if (options.hasClass('d-none')) {
                    $(this).find('i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
                } else {
                    $(this).find('i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
                }
            });
        }


    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('invoices.filter') }}

            <h1>{{__('common.filter')}}</h1>
            <form method="get" class="row" action="{{route('admin.invoices.result')}}">
                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('common.invoice-number')}}</p>
                        <button type="button" class="filter-button"> <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg></button>
                        <div class="options d-none">
                            <input type="text" name="invoice_number" placeholder="{{__('common.invoice-number')}}">
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('common.experts')}}</p>
                        <button type="button" class="filter-button"> <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg></button>
                        <div class="options d-none">
                            <select name="expert">
                                <option value="">{{__('common.please-choose')}}</option>
                                @foreach($experts as $expert)
                                    <option value="{{$expert->id}}">{{$expert->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('common.name-of-supplier')}}</p>
                        <button type="button" class="filter-button"> <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg></button>
                        <div class="options d-none">
                            <select name="invoice_name">
                                <option value="">{{__('common.please-choose')}}</option>
                                @foreach($invoices as $invoice)
                                    <option value="{{urlencode($invoice->name)}}">{{$invoice->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('common.date-of-issue')}}</p>
                        <button type="button" class="filter-button"> <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg></button>
                        <div class="options d-none">
                            <input type="text" name="date_of_issue" placeholder="{{__('common.date-of-issue')}}" class="datepicker">
                        </div>
                    </div>
                </div>

                <div class="filter-holder col-6">
                    <div class="filter">
                        <p>{{__('common.invoice-upload-date')}}</p>
                        <button type="button" class="filter-button"> <svg xmlns="http://www.w3.org/2000/svg" style="height: 30px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg></button>
                        <div class="options d-none">
                            <input type="text" name="created_at" placeholder="{{__('common.invoice-upload-date')}}" class="datepicker">
                        </div>
                    </div>
                </div>


                <div class="col-12 mt-5 mb-5">
                    <button type="submit" class="button btn-radius"> <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>{{__('common.filter')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
