@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/tasks.css?v=') . time()}}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{Breadcrumbs::render('affiliate-search-workflow.statistics')}}
            <h1>{{__('affiliate-search-workflow.menu')}}</h1>

            <x-affiliate-search.menu/>

            <div style="height: 50px;"></div>

            @if($over_deadline->count() > 0)
                <div class="mb-5">
                    <h1 class="text-center mb-2">{{__('task.over_deadline')}}</h1>
                    @foreach ($over_deadline as $data)
                        <div class="statistics-holder mb-2">
                            <span class="statistics-text-left">{{$data['user']}}</span>
                            <div class="statistics-line-holder">
                                <div class="statistics-line"
                                    style="width: {{calculate_percentage($data['points'], $data['all_points'])}}%; background-color: #a33095 !important"></div>
                            </div>
                            <span class="statistics-text-right">{{calculate_percentage($data['points'], $data['all_points'])}}% <small>({{$data['points'] . '/' . $data['all_points']}})</small></span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($last_day->count() > 0)
                <div class="mb-5">
                    <h1 class="text-center mb-2">{{__('task.last_day')}}</h1>
                    @foreach ($last_day as $data)
                        <div class="statistics-holder mb-2">
                            <span class="statistics-text-left">{{$data['user']}}</span>
                            <div class="statistics-line-holder">
                                <div class="statistics-line"
                                    style="width: {{calculate_percentage($data['points'], $data['all_points'])}}%; background-color: rgb(235, 126, 48) !important"></div>
                            </div>
                            <span class="statistics-text-right">{{calculate_percentage($data['points'], $data['all_points'])}}% <small>({{$data['points'] . '/' . $data['all_points']}})</small></span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($within_deadline->count() > 0)
                <div class="mb-5">
                    <h1 class="text-center mb-2">{{__('task.within_deadline')}}</h1>
                    @foreach ($within_deadline as $data)
                        <div class="statistics-holder mb-2">
                            <span class="statistics-text-left">{{$data['user']}}</span>
                            <div class="statistics-line-holder">
                                <div class="statistics-line"
                                    style="width: {{calculate_percentage($data['points'], $data['all_points'])}}%; background-color: #59c6c6 !important"></div>
                            </div>
                            <span class="statistics-text-right">{{calculate_percentage($data['points'], $data['all_points'])}}% <small>({{$data['points'] . '/' . $data['all_points']}})</small></span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
