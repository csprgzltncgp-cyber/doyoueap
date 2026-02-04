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
            color: rgb(0,87,95);
        }

        i.fa-plus-circle{
            float: right;
            color: #0d4197;
        }

        i.fa {
            font-size: 22px !important;
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
            <h1>{{ __('workshop.closed_workshops') }}</h1>
            <a href="{{route('admin.workshops.new')}}">{{ __('workshop.new_workshops') }}</a><br>
            <a href="{{route('admin.workshops.list')}}">{{ __('workshop.back_to_list') }}</a>
        </div>
        <div class="col-12 case-list-holder">
            @foreach($workshop_cases as $workshop)
                <div class="case-list" data-href="{{ route('admin.workshops.view', $workshop->id) }}" data-id="{{$workshop->id}}">
                    {{--<div class="case-list" data-href="{{route(\Auth::user()->type.'.cases.view',['id' => $case->id])}}" data-id="{{$case->id}}">--}}
                    <div>
                        @if($workshop->expert_status == 2)
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
</svg>
                        @elseif($workshop->expert_status == 3)
                            <i class="fa fa-exclamation-circle"></i>
                        @elseif($workshop->expert_status == 1)
                            <i class="fa fa-check-circle"></i>
                        @elseif($workshop->expert_status == null)

                        @endif
                        <h2>{{ $workshop->created_at }}</h2>
                        <span>{{ __('workshop.company_name') }}: <span id="company_name">{{ $workshop->companies_name }}</span></span><br>
                        <span>{{ __('workshop.company_contact_name') }}: <span id="company_contact_name">{{ $workshop->company_contact_name }}</span></span><br>
                        <span>{{ __('workshop.company_email') }}: <span id="company_contact_email">{{ $workshop->company_contact_email }}</span></span><br>
                        <span>{{ __('workshop.company_phone') }}: <span id="company_contact_phone">{{ $workshop->company_contact_phone }}</span></span><br>
                        <span>{{ __('workshop.country') }}: <span id="country_name">{{ $workshop->countries_name }}</span></span><br>
                        <span>{{ __('workshop.city') }}: <span id="city_name">{{ $workshop->city_name }}</span></span><br>
                        <span>{{ __('workshop.expert') }}: <span id="expert_name">{{ $workshop->user_name }}</span></span><br>
                        <span>{{ __('workshop.expert_email') }}: <span id="expert_mail"></span>{{ $workshop->user_email }}</span><br>
                        <span>{{ __('workshop.expert_phone') }}: <span id="expert_phone">{{ $workshop->expert_phone }}</span></span><br>
                        <span>{{ __('workshop.date') }}: <span id="date">{{ $workshop->date }}</span></span><br>
                        <span>{{ __('workshop.start_time') }}: <span id="start_time">{{ $workshop->start_time }}</span></span><br>
                        <span>{{ __('workshop.end_time') }}: <span id="end_time">{{ $workshop->end_time }}</span></span><br>
                        <span>{{ __('workshop.full_time') }}: <span id="full_time">{{ $workshop->full_time }}</span></span><br>
                        <span>{{ __('workshop.workshop_theme') }}: <span id="topic">{{ $workshop->topic }}</span></span><br>
                        <span>{{ __('workshop.activity_id') }}: <span id="activity_id">{{ $workshop->activity_id }}</span></span><br>
                        <span>{{ __('workshop.language') }}: <span id="language">{{ $workshop->language_name }}</span></span><br>
                        <span>{{ __('workshop.contract_price') }}: <span id="in_price">@if($workshop->free != 1) @if($workshop->workshops_price) {{ number_format("$workshop->workshops_price",0," "," ") }} @endif  @else Free @endif</span></span><br>
                        <span>{{ __('workshop.expert_out_price') }}: <span id="out_price">@if($workshop->price) {{ number_format("$workshop->price",0," "," ") }} @endif <span style="text-transform: uppercase"> {{$workshop->currency}}</span></span><br>
                        <span>{{ __('workshop.expert_in_price') }}: <span id="out_price">@if($workshop->expert_price) {{ number_format("$workshop->expert_price",0," "," ") }} @endif <span style="text-transform: uppercase"> {{$workshop->expert_currency}}</span></span><br>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
