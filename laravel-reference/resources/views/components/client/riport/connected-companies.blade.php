@props([
    'total' => false,
    'connectedCompanies',
    'currentCompany',
    'route',
])

@if(count($connectedCompanies) > 1)
<div class="flex flex-wrap justify-center py-2 space-x-5 text-white uppercase bg-purple">
    @foreach($connectedCompanies->sortBy('name') as $connectedCompany)
        {{-- Hide Valeo Autoklimatizace k.s. beacues this company is just for invoicing, all cases moved to subcompanies --}}
            @if($connectedCompany->id == 577)
                @continue
            @endif
       {{-- Hide Valeo Autoklimatizace k.s. beacues this company is just for invoicing, all cases moved to subcompanies --}}

       <form action="{{route('client.riport.login_to_connected_company')}}" method="get">
            @csrf
            <input type="hidden" name="user_id" value="{{$connectedCompany->clientUsers->first()->id}}"/>
            <input type="hidden" name="route" value="{{$route}}"/>
            <button
                type="submit"
                class="
                    float-right submit uppercase cursor-pointer put-loader-on-click
                    @if(!$total && $connectedCompany->id == $currentCompany->id)underline @endif
                    "
            >
                {{$connectedCompany['name']}}
            </button>
       </form>
    @endforeach
</div>
@endif
