@props([
    'name',
    'values',
])

<div class="flex flex-col">
    <div class="mb-3 h-96 flex justify-between px-5">
        @foreach(collect($values)->reverse() as $value_name => $data)
            <div class="flex flex-col justify-end items-center" style="width: 40%">
                @if($loop->first)
                    <img src="{{asset('assets/img/client/riport/female-green.svg')}}" alt="icon" style="height: 50px;" class="mb-3 mx-auto">
                @else
                    <img src="{{asset('assets/img/client/riport/male-green.svg')}}" alt="icon" style="height: 50px;" class="mb-3 mx-auto">
                @endif
                <div class="h-3/4 bg-green-light rounded-full w-4/5 mx-auto" style="height: {{calculate_percentage($data['count'], $data['total_count'], 0) == 0 ? 3 :calculate_percentage($data['count'], $data['total_count'], 0)}}%;"></div>
                <span class="text-green-light text-3xl font-bold mt-3">{{calculate_percentage($data['count'], $data['total_count'])}}%</span>
            </div>
        @endforeach
    </div>
    <div class="bg-green-light h-0.5 mb-10"></div>
    <p class="text-2xl uppercase font-bold mx-auto text-center">{{$name}}</p>
</div>
