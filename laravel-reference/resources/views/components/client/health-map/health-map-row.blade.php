@props([
    'text',
    'circles',
    'problemTypeId',
    'light' => false,
])

<div class="grid grid-cols-7 gap-1 w-full relative mt-1" style="height: 213px">
    <div class="bg-green-light {{$light ? : 'opacity-70'}}  w-full"></div>
    <div class="bg-green-light {{$light ? : 'opacity-70'}}  w-full"></div>
    <div class="bg-green-light {{$light ? : 'opacity-70'}}  w-full"></div>
    <div class="bg-green-light {{$light ? : 'opacity-70'}}  w-full"></div>
    <div class="bg-green-light {{$light ? : 'opacity-70'}}  w-full"></div>
    <div class="bg-green-light {{$light ? : 'opacity-70'}}  w-full"></div>
    <div class="bg-green-light {{$light ? : 'opacity-70'}}  w-full"></div>
    <p class="w-full text-center pt-5 uppercase text-8xl md:text-10xl lg:text-11xl text-gray-100  opacity-50 font-bold absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
        {{$text}}
    </p>

    @foreach($circles as $gender_id => $gender)
        @foreach($gender as $data_id => $data)
            @if($data['problem_type_id'] == $problemTypeId && $data['age_id'] != 17)
                {{--   do not show "unknown" ages--}}
                @php
                    $circleComponentName = "client.health-map." . $data['size'] . "-circle";
                    $id = $data_id . "-" . $gender_id;
                @endphp
                <x-dynamic-component
                        :id="$id"
                        :age="$data['age_id']"
                        :component="$circleComponentName"
                        :gender="$gender_id"
                />
            @endif
        @endforeach
    @endforeach
</div>
