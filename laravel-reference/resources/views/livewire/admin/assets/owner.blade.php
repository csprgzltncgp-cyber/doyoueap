<div>
    <form wire:submit.prevent>
        {{csrf_field()}}
        <div class="form-row ml-0 w-100">
            <div class="form-group mb-0">
                <div class="d-flex flex-column">
                    <div class="input-group p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('asset.name')}}:
                            </div>
                        </div>
                        <input type="text" class="w-100" size="{{strlen($owner->name)+3}}" wire:model="owner.name">
                    </div>
                </div>
            </div>

            <div class="form-group mb-0 pr-md-0 ml-2">
                <div class="d-flex flex-column">
                    <div class="input-group p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('asset.country')}}:
                            </div>
                        </div>
                        <select wire:model="owner.country_id" required>
                            <option value="">{{__('workshop.select')}}</option>
                            @foreach($countries as $country)
                                <option value="{{$country->id}}">{{$country->code}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <!-- List equipments from the assets that the "owner" has -->
            @foreach($owner->assets as $asset)
                @livewire('admin.assets.asset', ['asset' => $asset, 'index' => $loop->index, 'is_search_result' => false], key('asset_'.$asset->id))
            @endforeach
            @if ($show_type)
                <div class="form-row">
                    <div class="form-group col-md-0 mb-0">
                        <div class="d-flex flex-column">
                            <div class="input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="eq-data">
                                        <span class="eq-title mr-3">{{ $owner->assets->count() + 1 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-4 mb-0 pr-md-0">
                        <div class="d-flex flex-column">
                            <div class="input-group col-12 p-0">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        {{ __('asset.type') }}:
                                    </div>
                                </div>
                                <select wire:model="new_asset_type" wire:change="new_asset_step_2()" required>
                                    <option value=""></option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row mb-3 mt-3">
                <div class="col-sm-auto d-flex align-items-center pr-0">
                    <button type="button"
                        style="text-transform: uppercase;"
                        wire:click="new_asset_step_1()" class="text-center btn-radius">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                            style="width: 20px; height:20px;" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span class="mt-1">
                            {{ __('common.add') }}
                        </span>
                    </button>
                </div>
    
                <div class="row mb-3 mt-3">
                    <div class="col-sm-auto d-flex align-items-center">
                        <button type="button"
                            style="padding-bottom: 14px; padding-left:10px; text-transform: uppercase;"
                            wire:click="save()"
                            class="text-center btn-radius">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                style="height: 20px; width:20px;" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                </path>
                            </svg>
                            <span class="mt-1">
                                {{ __('common.save') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
