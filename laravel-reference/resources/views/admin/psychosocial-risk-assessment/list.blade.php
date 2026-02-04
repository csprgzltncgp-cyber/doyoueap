@extends('layout.master')

@section('extra_css')
<link rel="stylesheet" href="/assets/css/riports/riports.css?v={{time()}}">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        {{ Breadcrumbs::render('psychosocial-risk-assessment') }}

        <h1>{{__('common.psychosocial_risk_assessment')}}</h1>
        <div class="excel-export-holders">
            <div class="w-100">
                <div class="flex flex-column">
                    <div class="export-riport-to-excel">
                        <span class="date">Ivy technology</span>
                        <a href="{{route('admin.psychosocial-risk-assessment.download_ivy_summary')}}"
                            style="color:white !important;">
                            <span class="export">{{__('common.export-riport-excel')}}</span>
                        </a>
                    </div>
                </div>
                <div class="flex flex-column">
                    <div class="export-riport-to-excel">
                        <span class="date">ExxonMobil</span>
                        <a href="{{route('admin.psychosocial-risk-assessment.download_exxon_summary')}}"
                            style="color:white !important;">
                            <span class="export">{{__('common.export-riport-excel')}}</span>
                        </a>
                    </div>
                </div>
                <div class="flex flex-column">
                    <div class="export-riport-to-excel">
                        <span class="date">Schott</span>
                        <a href="{{route('admin.psychosocial-risk-assessment.download_schott_summary')}}"
                            style="color:white !important;">
                            <span class="export">{{__('common.export-riport-excel')}}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
