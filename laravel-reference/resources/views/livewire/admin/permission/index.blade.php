<div class="w-100">
    <x-invoice-helper.search />

    <div wire:loading.delay class="w-100">
        <img style="width: 40px; height: 40px; margin:0 auto; width:100%;" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
    </div>
    @foreach($companies as $company)
        <div class="list-element col-12">
            <span>{{$company->name}}</span>
            <a class="float-right" href="{{route(auth()->user()->type . '.companies.permissions.edit',['id' => $company->id])}}">
                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                        style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{__('common.edit')}}</a>
        </div>
    @endforeach
</div>
