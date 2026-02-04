<div class="list-element col-12">
    <span class="data mr-0">
        {{$podcasts->short_title}}
         - {{$podcasts->getVisibilities()}}
    </span>

    <a class="edit-workshop btn-radius" style="--btn-margin-left: var(--btn-margin-x)"
       href="{{route('admin.eap-online.podcasts.edit',['id' => $podcasts->id])}}">
       <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
       {{__('common.select')}}
    </a>
</div>
