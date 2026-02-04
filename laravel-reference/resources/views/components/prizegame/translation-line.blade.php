@props([
    'id',
    'model',
    'languages',
    'class' => Str::of(get_class($model))->afterLast('\\')->lower()->value,
])

<form method="POST" action="{{route('admin.prizegame.translation.pages.store')}}">
    @csrf
    <div class="col-12 row">
        <div class="col-12 input">
            <div class="row">
                <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                    onclick="toggleTranslationSection('{{$class}}-{{$id}}-translations', this)"
                >
                    <p class="m-0 mr-3">{{$class}}</p>
                    <div class="d-flex align-items-center">
                        <div class="d-flex flex-wrap">
                            @foreach($languages as $language)
                                @if(!empty($model->has_translation($language)))
                                    <div style="background-color:rgb(145,183,82);"
                                        class="px-2 text-white mr-3 mb-2">
                                        {{$language->code}}
                                    </div>
                                @else
                                    <div style="background-color:rgb(219, 11, 32);"
                                        class="px-2 text-white mr-3 mb-2">
                                        {{$language->code}}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" style="min-width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                    </div>
                </div>
            </div>
            <div class="d-none" id="{{$class}}-{{$id}}-translations">
                @foreach($languages as $language)
                    <div class="row translation">
                        <div class="col-1 text-center" style="padding-top:15px;">
                            {{$language->code}}
                        </div>
                        <div class="col-8 pl-0">
                            <textarea name="translations[{{$language->id}}]" placeholder="{{__('eap-online.system.translation')}}">{{$model->has_translation($language) ? $model->get_translation($language) : ''}}</textarea>
                        </div>
                    </div>
                @endforeach

                <input type="hidden" name="model" value="{{get_class($model)}}">
                <input type="hidden" name="id" value="{{$id}}">

                <button type="submit" >{{__('common.save')}}</button>
            </div>
        </div>
    </div>
</form>
