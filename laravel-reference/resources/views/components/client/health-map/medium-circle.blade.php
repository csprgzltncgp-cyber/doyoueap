@props([
    'gender',
    'age',
    'id'
])

@php
    $left = ($age - 10) * (100 / 7);
@endphp

<div data-id="{{$id}}" class="{{$gender == 10 ? 'bg-purple' : 'bg-yellow'}} p-7 rounded-full flex items-center justify-center z-40 absolute top-0 absolute top-1/2  circle"
     style="left: {{$left}}%; box-shadow: -10px 20px 30px rgba(0,0,0,0.4);"
>
    @if($gender == 10)
        <img src="{{asset('assets/img/client/riport/female-white.svg')}}" alt="female" class="h-20 w-20">
    @else
        <img src="{{asset('assets/img/client/riport/male-white.svg')}}" alt="male" class="h-20 w-20">
    @endif
</div>