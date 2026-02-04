@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
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
            margin-right: 30px;
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

        a#filter {
            color: white;
            font-weight: bold;
            background-color: rgb(89, 198, 198);
            border-radius: 0px;
            text-transform: uppercase;
            padding: 10px 40px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 2px solid transparent;
            outline: none !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 ">
            {{ Breadcrumbs::render('feedback.messages') }}
            <h1>{{__('feedback.menu')}}</h1>
            <a href="{{route('admin.feedback.filter.view')}}" id="filter" class="mb-4 mt-3 btn-radius"
            style="--btn-max-width: var(--btn-min-width)">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>{{__('workshop.apply_filter')}}</a>
        </div>
        <div class="col-12">
            @if($feedbacks->count() > 0)
                @foreach($feedbacks->reverse() as $feedback)
                    <div class="list-element col-12 @if(empty($feedback->viewed_at)) unread @endif">
                        <div style="text-decoration: none"
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

                                <a href="{{route('admin.feedback.delete', ['feedback' => $feedback])}}">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    {{__('common.delete')}}
                                </a>

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
        <div class="col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.feedback.actions') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
@endsection
