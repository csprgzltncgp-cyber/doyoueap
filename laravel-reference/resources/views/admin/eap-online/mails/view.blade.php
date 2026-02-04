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
            {{ Breadcrumbs::render('eap-online.mails.show', $mail) }}
            <h1>{{$mail->eap_user->username}} - {{$mail->subject}} - {{$mail->category}}
                - {{$mail->eap_user->company->name}}
                - {{$mail->date}}</h1>
            <a href="{{route('admin.eap-online.mails.list')}}">{{__('common.back-to-list')}}</a>
        </div>
        <div class="col-12">
            @foreach($mail->eap_messages->sortBy('created_at') as $message)
                <div class="list-elem d-flex flex-column"
                     @if($message->type == 'admin' || $message->type == 'operator') style="background: rgba(163 ,48 ,150 , 0.2)" @endif>
                    <div class="d-flex justify-content-between">
                        <p>{{__('common.from')}}: {{$message->sender()->username}}</p> <p>{{$message->created_at}}</p>
                    </div>
                    <p style="font-family: CalibriI; font-weight: normal;">{!! $message->message !!}</p>
                    @if($message->eap_attachments->count() > 0)
                        <div class="d-flex mt-3">
                            <p class="m-0 mr-1">{{__('common.attachments')}}:</p>
                            <div class="d-flex flex-wrap">
                                @foreach($message->eap_attachments as $attachment)
                                    <a class=" mr-3"
                                       href="{{$attachment->url}}">{{substr($attachment->url, strrpos($attachment->url, '/') + 1)}}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
            @if(!isset($mail->deleted_at))
                <div class="list-elem">
                    <form action="{{route('admin.eap-online.mails.reply', ['id' => $mail->id])}}" method="post"
                          class="d-flex flex-column align-items-end">
                        @csrf
                        <textarea name="message" cols="30" rows="5"
                                  placeholder="{{__('eap-online.mails.response')}}" required></textarea>
                        <button type="submit" class="button btn-radius float-right d-flex align-items-center">
                            <img class="mr-1" src="{{asset('assets/img/send.svg')}}" style="height: 20px; width: 20px" alt="">
                            <span>{{__('common.send')}}</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
