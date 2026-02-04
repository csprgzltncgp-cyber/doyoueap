<div class="list-element col-12">
    <span class="data mr-0">
        {{$webinars->short_title}}
         - {{$webinars->getVisibilities()}}
    </span>

    <a class="edit-workshop btn-radius" style="--btn-margin-left: var(--btn-margin-x"
        href="{{route('admin.eap-online.webinars.edit',['id' => $webinars->id])}}">
        <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px;)" alt="">
        {{__('common.select')}}
    </a>
</div>
