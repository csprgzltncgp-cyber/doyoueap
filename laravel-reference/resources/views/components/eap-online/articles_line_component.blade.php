<div class="list-element col-12">
    <span class="data mr-0">
    {{implode(' ', array_slice(explode(' ', $articles->getSectionByType('headline')), 0, 5))}}
         - {!! $articles->getVisibilities() !!}
    </span>

    @if($translation)
        <a class="edit-workshop btn-radius" style="--btn-margin-left: var(--btn-margin-x);"
           href="{{route('admin.eap-online.articles.translate.view',['id' => $articles->id])}}">
           <img src="{{asset('assets/img/select.svg')}}" style="height: 20px; width: 20px" alt="">
           {{__('common.select')}}</a>
        @foreach($articles->getReadyLanguages() as $language_code => $ready)
            @if($ready)
                <div style="background-color:rgb(145,183,82); margin-right: 10px"
                     class="px-3 text-white">
                    {{$language_code}}
                </div>
            @endif
        @endforeach
    @else
        <a class="edit-workshop btn-radius" style="--btn-margin-left: var(--btn-margin-x);"
           href="{{route('admin.eap-online.articles.edit',['id' => $articles->id])}}">
           <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
           {{__('common.select')}}</a>
    @endif
</div>
