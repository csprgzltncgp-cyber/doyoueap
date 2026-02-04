@extends('layout.employee')

@section('content')
<h1 style="text-align: center; color: black">
    Kérjük erősítse meg a jelentkezését.
    </br>
    Please confirm your application.
</h1>
<a href="{{ route('employee.case', $params) }}" class="mt-4" style="text-decoration: none;">
    <button class="px-5 py-2" style="border-width: 2px; border-style: solid; border-radius: 9999px; border-color: rgb(163 48 149); color: rgb(163 48 149); background-color: white; outline: none;">Megerősít/Confirm</button>
</a>
@endsection
