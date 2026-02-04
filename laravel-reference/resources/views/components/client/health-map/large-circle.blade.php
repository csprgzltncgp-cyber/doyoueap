@props([
    'gender',
    'age',
    'id'
])

@php
    $left = ($age - 10) * (100 / 7);
@endphp

<div data-id="{{$id}}" class="{{$gender == 10 ? 'bg-purple' : 'bg-yellow'}} p-10 rounded-full flex items-center justify-center z-30 absolute top-1/2  circle"
     style="left: {{$left}}%; box-shadow: -10px 20px 30px rgba(0,0,0,0.4);"
>
    @if($gender == 10)
        <img src="{{asset('assets/img/client/riport/female-white.svg')}}" alt="female" class="h-24 w-24">
    @else
        <img src="{{asset('assets/img/client/riport/male-white.svg')}}" alt="male" class="h-24 w-24">
    @endif
</div>