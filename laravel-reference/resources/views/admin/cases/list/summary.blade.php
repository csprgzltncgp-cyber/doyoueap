@extends('layout.master')

@section('title', 'Supervisor Dashboard')

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <style>
        .search-container{
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        .search-holder{
            display: flex;
            align-items: center;
            flex: 1 0 auto;
        }

        .search-holder input{
            flex: 1 0 auto;
            margin: 0px 5px 0px 0px;
            border: 2px solid rgb(89,198,198);
            padding: 10px 0px 10px 15px;
            outline: none !important;
        }

        .green-box{
            background: rgba(89, 198, 198, 0.2);
            padding: 12px;
        }

        .green-box.button-c {
            background: #59c6c6;
            color: white;
            text-align: center;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            text-transform: uppercase;
        }
    </style>
@endsection

@section('content')
    @livewire('admin.case-summary.index')
@endsection
