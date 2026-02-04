<div>
    <form wire:submit.prevent="store" style="max-width: 1500px !important;">
        <div class="form-row">
            <div class="form-group col-md-3 mb-0">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('asset.name')}}:
                            </div>
                        </div>
                        <input type="text" class="col-12" wire:model="owner.name" >
                    </div>
                </div>
            </div>

            <div class="form-group col-md-4 mb-0 pr-md-0">
                <div class="d-flex flex-column">
                    <div class="input-group col-12 p-0">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{__('asset.country')}}:
                            </div>
                        </div>
                        <select name="country_id" wire:change="" wire:model="owner.country_id" required>
                            <option value=""></option>
                            @foreach($countries as $country)
                                <option value="{{$country->id}}"
                                        @if($owner->country_id == $country->id) selected @endif>{{$country->code}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
