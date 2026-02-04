@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/list.css?v={{time()}}">

    <style>
        i.fa-exclamation-triangle{
            float: right;
            color: #f2da2f;
        }

        i.fa-exclamation-circle{
            float: right;
            color: #ff041d;
        }

        i.fa-check-circle{
            float: right;
            color: #00660c;
        }

        i.fa-plus-circle{
            float: right;
            color: #0d4197;
        }
    </style>
@endsection

@section('extra_js')
    <script>
        var select = 0;

        function selectClick(){
            select = !select;
            if(select){
                $('#selectButton').addClass('active');
            }else{
                $('form[name="excel_export"] input[name!="_token"]').remove();
                $('#selectButton').removeClass('active');
                $('.case-list.selected').removeClass('selected');
            }
        }

        $(function(){
            clickOnCase();
        });

        function clickOnCase(){
            $('.case-list-holder').on('click','.case-list',function(e){
                if(!select){
                    const url = $(this).data('href');
                    window.location.href = url;
                }

                $(this).toggleClass('selected');
                const id = $(this).data('id');
                if($(this).hasClass('selected')){
                    const input = '<input type="hidden" name="cases[]" value="' + id + '">';
                    $('form[name="excel_export"]').append(input);
                }
                else{
                    $('form[name="excel_export"] input[value="' + id + '"]').remove();
                }
            });
        }

    </script>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <h1>Lezárt crisisok</h1>
            <a href="{{route('admin.crisis.new')}}">Új crisis felvitele</a><br>
        </div>
        <div class="col-12 case-list-holder">
            @foreach($crisis_cases as $crisis)
                <div class="case-list" data-href="{{ route('admin.crisis.view', $crisis->id) }}" data-id="{{$crisis->id}}">
                    {{--<div class="case-list" data-href="{{route(\Auth::user()->type.'.cases.view',['id' => $case->id])}}" data-id="{{$case->id}}">--}}
                    <div>
                        @if($crisis->expert_status == 2)
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
</svg>
                        @elseif($crisis->expert_status == 3)
                            <i class="fa fa-exclamation-circle"></i>
                        @elseif($crisis->expert_status == 1)
                            <i class="fa fa-check-circle"></i>
                        @elseif($crisis->expert_status == null)

                        @endif
                        <h2>{{ $crisis->created_at }}</h2>
                        <span>Cég neve: <span id="company_name">{{ $crisis->companies_name }}</span></span><br>
                        <span>Cég kapcsolattartó neve: <span id="company_contact_name">{{ $crisis->company_contact_name }}</span></span><br>
                        <span>Cég email címe: <span id="company_contact_email">{{ $crisis->company_contact_email }}</span></span><br>
                        <span>Cég telefonszáma: <span id="company_contact_phone">{{ $crisis->company_contact_phone }}</span></span><br>
                        <span>Ország: <span id="country_name">{{ $crisis->countries_name }}</span></span><br>
                        <span>Város: <span id="city_name">{{ $crisis->city_name }}</span></span><br>
                        <span>Szakértő: <span id="expert_name">{{ $crisis->user_name }}</span></span><br>
                        <span>Szakértő email címe: <span id="expert_mail"></span>{{ $crisis->user_email }}</span><br>
                        <span>Szakértő telefonszáma: <span id="expert_phone">{{ $crisis->expert_phone }}</span></span><br>
                        <span>Dátum: <span id="date">{{ $crisis->date }}</span></span><br>
                        <span>Kezdési idő: <span id="start_time">{{ $crisis->start_time }}</span></span><br>
                        <span>Befejezési idő: <span id="end_time">{{ $crisis->end_time }}</span></span><br>
                        <span>Teljes crisis idő: <span id="full_time">{{ $crisis->full_time }}</span></span><br>
                        <span>Topic: <span id="topic">{{ $crisis->topic }}</span></span><br>
                        <span>Activity Id: <span id="activity_id">{{ $crisis->activity_id }}</span></span><br>
                        <span>Nyelv: <span id="language">{{ $crisis->language_name }}</span></span><br>
                        <span>Szerződéses ár: <span id="in_price">@if($crisis->crisis_interventions_price) {{ number_format("$crisis->crisis_interventions_price",0," "," ") }} @endif <span style="text-transform: uppercase"> {{$crisis->crisis_interventions_valuta}}</span></span></span><br>
                        <span>Szakértő díjazása: <span id="out_price">@if($crisis->price) {{ number_format("$crisis->price",0," "," ") }} @endif <span style="text-transform: uppercase"> {{$crisis->currency}}</span></span></span><br>
                        <span>Szakértő árajánlata: <span id="out_price">@if($crisis->expert_price) {{ number_format("$crisis->expert_price",0," "," ") }} @endif <span style="text-transform: uppercase"> {{$crisis->expert_currency}}</span></span></span><br>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
