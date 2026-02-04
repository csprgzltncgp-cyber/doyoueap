@props([
    'images',
    'name',
    'values'
])

<div class="w-full flex flex-col justify-center items-center space-y-28 pt-28">
    <div class="flex flex-col justify-center items-center w-full">
        @if(!empty($images))
            <div class="flex justify-center space-x-5">
                @foreach ($images as $image)
                    <img src="{{$image}}" alt="img"
                        class="h-14"
                    >
                @endforeach
            </div>
        @endif
        <p class="font-bold text-2xl mt-3 mb-10">{{\Illuminate\Support\Str::upper($name)}}</p>

        @foreach($values as $type_name => $type_values)
            @php
                foreach ($type_values as $key => $value) {
                    if (is_array($value)) {
                        $type_values[$key] = collect($value)->sum();
                    }
                }

                $total_count = 0;

                foreach ($values as $value){
                    $total_count += collect($value['count'])->sum();
                }

                $type_values['total_count'] = $total_count;

                // Remove the word "suicide" from the case input value line 59.
                if ($type_values['id'] === 59) {
                    $type_name = Str::of($type_name)->afterLast(', ')->ucfirst();
                }

                if(($percentage = (int) calculate_percentage($type_values['count'], $type_values['total_count'])) <= 0){
                    continue;
                }
            @endphp
            <x-client.riport.vertical-percentage name="{{$type_name}}" :percentage="$percentage"/>
        @endforeach
    </div>
</div>
