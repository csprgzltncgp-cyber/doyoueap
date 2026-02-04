<div class="list-element col-12">
    <span class="data mr-0">
    {{$quizzes->title_translations()->where('language_id', $quizzes->input_language)->first()->value}}
         - {{$quizzes->getVisibilities()}}
    </span>

    @if($translation)
        <a class="edit-workshop btn-radius" style="--btn-margin-left: var(--btn-margin-x)"
           href="{{route('admin.eap-online.quizzes.translate.view',['id' => $quizzes->id])}}">
           <img src="{{asset('assets/img/select.svg')}}" style="height: 20px; width: 20px" alt="">
           {{__('common.select')}}
        </a>
        @foreach($quizzes->getReadyLanguages() as $language_code => $ready)
            @if($ready)
                <div style="background-color:rgb(145,183,82); margin-right: 10px"
                     class="px-3 text-white">
                    {{$language_code}}
                </div>
            @endif
        @endforeach
    @else
        <a class="edit-workshop btn-radius" style="--btn-margin-left: var(--btn-margin-x)"
           href="{{route('admin.eap-online.quizzes.edit_view', ['id' => $quizzes->id])}}">
           <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
           {{__('common.select')}}
        </a>
    @endif
</div>
