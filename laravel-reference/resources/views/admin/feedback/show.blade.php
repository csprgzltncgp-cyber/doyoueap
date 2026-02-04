@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <style>
        .list-elem {
            padding: 20px 40px;
            background: rgb(222, 240, 241);
            color: black;
            margin-right: 10px;
            margin-bottom: 10px;
            min-width: 200px;
        }

        button {
            padding: 10px 15px;
            background: rgb(0, 87, 95);
            border: none;
            color: white;
            text-transform: uppercase;
            margin-right: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5">
            {{ Breadcrumbs::render('feedback.messages.show', $feedback) }}
            <h1>
                {{$feedback->company}}
                @if(!empty($feedback->email)) - {{$feedback->email}} @endif
                @if(!empty($feedback->consultation)) - {{$feedback->consultation}} @endif
                @if(!empty($feedback->expert)) - {{$feedback->expert}} @endif
            </h1>
            <a href="{{route('admin.feedback.index')}}">{{__('common.back-to-list')}}</a>
        </div>
        <div class="col-12">
            @foreach($feedback->messages->sortBy('created_at') as $message)
                <div class="list-elem d-flex flex-column"
                     @if($message->type == \App\Models\Feedback\Message::TYPE_ADMIN) style="background: rgba(163 ,48 ,150 , 0.2)" @endif>
                    <div class="d-flex justify-content-between">
                        <p>{{$message->created_at}}</p>
                    </div>
                    <p style="font-family: CalibriI; font-weight: normal;">{!! $message->value !!}</p>
                </div>
            @endforeach
            @if(!empty($feedback->email) && $feedback->messages->count() < 2)
                <div class="list-elem">
                    <form action="{{route('admin.feedback.reply', ['feedback' => $feedback])}}" method="post"
                          class="d-flex flex-column align-items-end">
                        @csrf
                        <textarea name="message" cols="30" rows="5"
                                  placeholder="{{__('feedback.reply_placeholder')}}" required></textarea>
                        <button type="submit" class="button btn-radius float-right d-flex align-items-center">
                            {{__('common.send')}}</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
