@extends('layout.client.master', ['bg' =>'crisis'])

@section('extra_css')
    <link rel="stylesheet" href="{{ asset('assets/css/client/flip-card.css') }}">
@endsection

@section('content')
    <div x-data="{country: {{Auth::user()->country->id}}}">

        <x-client.riport.connected-companies
            :connectedCompanies="$connected_companies"
            :currentCompany="$company"
            route="client.crisis-interventions"
        />

        @if(Auth::user()->all_country && $crisis_interventions->count() > 0)
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
        <div class="grid grid-cols-4 gap-1 mb-20">
            @foreach($crisis_interventions->groupBy('country_id') as $crisis_country_group)
                @foreach($crisis_country_group as $crisis)
                    @component('components.crisises.crisis_card_component',
                         [
                             'crisis' => $crisis,
                         ])@endcomponent
                @endforeach
            @endforeach
        </div>
    </div>
@endsection
