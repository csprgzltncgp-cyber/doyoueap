<div class="search-container">
    <div class="order-holder">
        <svg xmlns="http://www.w3.org/2000/svg" style="heigth:20px; width:20px;" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
            </svg>
        <span class="mr-2">{{__('invoice-helper.sort')}}:</span>
        <select wire:model="sort">
            <option value="asc">{{__('invoice-helper.ascending-by-name')}}</option>
            <option value="desc">{{__('invoice-helper.descending-by-name')}}</option>
        </select>
    </div>

    <div class="search-holder row">
        <div class="green-box button-c btn-radius" style="--btn-height:auto; --btn-margin-bottom: 0px;">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                {{__('invoice-helper.new-filter')}}
        </div>

        <input class="btn-input-field-height" placeholder="{{__("common.search")}}" wire:model='search'>

        <div class="green-box button-c btn-radius" style="--btn-height:auto; --btn-margin-left: var(--btn-margin-x); --btn-margin-bottom: 0px;" wire:click="resetSearch">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{__('invoice-helper.delete-filter')}}
        </div>
    </div>
</div>
