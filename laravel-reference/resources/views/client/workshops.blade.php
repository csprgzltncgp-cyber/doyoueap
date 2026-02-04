@extends('layout.client.master', ['bg' =>'workshop'])

@section('extra_css')
    <link rel="stylesheet" href="{{ asset('assets/css/client/flip-card.css') }}">
@endsection

@section('content')
    <div x-data="{country: {{Auth::user()->country->id}}}">

        <x-client.riport.connected-companies
            :connectedCompanies="$connected_companies"
            :currentCompany="$company"
            route="client.workshops"
        />

        @if(Auth::user()->all_country && $workshops->count() > 0)
            <div class="flex justify-center py-2 space-x-5 text-black uppercase bg-white bg-opacity-60">
                @foreach($countries->sortBy('name') as $country)
                    <span class="cursor-pointer"
                          x-on:click="country = {{$country->id}}"
                          :class="country === {{$country->id}} ? 'underline' : ''">
                        {{$country->name}}
                    </span>
                @endforeach
            </div>
        @endif
        <div class="grid grid-cols-2 md:grid-cols-4  gap-1 mb-20">
            @foreach($workshops->groupBy('country_id') as $workshops_country_group)
                @foreach($workshops_country_group as $workshop)
                    @component('components.workshops.workshop_card_component',
                         [
                             'workshop' => $workshop,
                         ])@endcomponent
                @endforeach
            @endforeach
        </div>
    </div>
@endsection
