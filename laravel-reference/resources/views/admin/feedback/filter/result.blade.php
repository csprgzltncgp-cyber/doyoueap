@extends('layout.master')

@section('title')
    Operator Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/list_in_progress.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/master.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <style>
        .list-elem {
            background: rgb(222, 240, 241);
            color: black;
            margin-right: 10px;
            min-width: 200px;
        }

        a.button {
            padding: 20px 40px;
            background: rgb(0, 87, 95);
            border: none;
            color: white;
            text-transform: uppercase;
            margin-right: 10px;
        }

        .list-element button, .list-element a {
            margin-right: 10px;
            display: inline-block;
        }

        .list-element button.delete-button {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            border: 0px solid black;
            color: #007bff;
            outline: none;
        }

        .list-element {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .unread {
            background-color: rgb(89, 198, 198);
            color: white;
        }

        .unread a {
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="row m-0 w-100">
        {{ Breadcrumbs::render('feedback.messages.filtered') }}
        <h1 class="col-12 pl-0">{{__('workshop.filter_result')}}</h1>
        <ul id="workshop-submenus" class="w-100">
            <li>
                <a class="col-12 pl-0 d-block add-new-workshop btn-radius" href="{{route('admin.feedback.filter.view')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                   {{__('workshop.apply_filter')}}
                </a>
            </li>
            <li>
                <a class="col-12 pl-0 d-block add-new-workshop btn-radius" href="{{route('admin.feedback.index')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                      </svg>
                    {{__('eap-online.articles.list')}}
                </a>
            </li>
        </ul>

        @if(!$feedbacks->count())
            <p>{{__('workshop.no_filter_result')}}</p>
        @endif
        <div class="col-12 row">
            @if($feedbacks->count() > 0)
                @foreach($feedbacks->reverse() as $feedback)
                    <div  class="list-element col-12 @if(empty($feedback->viewed_at)) unread @endif">
                        <div style="text-decoration: none; margin-bottom: 0"
                             href="{{route('admin.feedback.show', ['feedback' => $feedback])}}"
                             class="d-flex justify-content-between align-items-center w-100">
                            <div class="list-elem @if(empty($feedback->viewed_at)) unread @endif">
                                <span>{{$feedback->created_at}}</span>
                                -
                                <span>{{$feedback->company}}</span>
                            </div>
                            <div class="d-flex">
                                <a href="{{route('admin.feedback.set-unread', ['feedback' => $feedback])}}">{{__('eap-online.mails.restore_notification')}}</a>
                                <a href="{{route('admin.feedback.show', ['feedback' => $feedback])}}">{{__('common.select')}}</a>

                                @if($feedback->type == \App\Models\Feedback\Feedback::TYPE_POSITIVE)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                         style="height: 20px; @if(empty($feedback->viewed_at)) color:white; @else color: rgb(115,144,52);@endif"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                    </svg>
                                    <span style="@if(empty($feedback->viewed_at)) color:white; @else color: rgb(115,144,52);@endif">{{__('feedback.positive_feedback')}}</span>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                         style="height: 20px;@if(empty($feedback->viewed_at)) color:white; @else color:rgb(127,64,116);@endif"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/>
                                    </svg>
                                    <span style="@if(empty($feedback->viewed_at)) color:white; @else color:rgb(127,64,116);@endif">{{__('feedback.negative_feedback')}}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
