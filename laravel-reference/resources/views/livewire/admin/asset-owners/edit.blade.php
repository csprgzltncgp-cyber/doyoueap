
<div>
    <form wire:submit.prevent="update" class="mt-0" style="max-width: 1500px !important;" autocomplete="off" novalidate>
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
                        <input type="text" class="w-100" size="{{strlen($owner->name)}}" wire:model="owner.name">
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
                        <select name="country_id" wire:model="owner.country_id" required>
                            <option value="">{{__('workshop.select')}}</option>
                            @foreach($countries as $country)
                                <option value="{{$country->id}}">{{$country->code}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
