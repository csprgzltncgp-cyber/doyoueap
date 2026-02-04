@props([
    'percentage',
    'name'
])

<div class="w-full grid grid-cols-3 gap-5 items-center justify-center">
    <span class="text-3xl justify-self-end text-right">{!! $name !!}</span>
    <div class="bg-gray-200 bg-opacity-60 h-2.5 grow rounded-full">
        <div class="bg-yellow h-2.5 rounded-full"
             style="width: {{$percentage}}%;"></div>
    </div>
    <span class="text-3xl">{{$percentage}}%</span>
</div>
