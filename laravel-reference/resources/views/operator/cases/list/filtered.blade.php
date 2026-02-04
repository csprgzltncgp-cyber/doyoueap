@extends('layout.master')

@section('title')
Operator Dashboard
@endsection

@section('extra_css')
<link rel="stylesheet" href="/assets/css/cases/list.css?v={{time()}}">
@endsection

@section('extra_js')
<script>
  var select = 0;

  function selectClick(){
    select = !select;
    if(select){
      $('#selectButton').addClass('active');
    }else{
      $('form[name="excel_export"] input[name!="_token"]').remove();
      $('#selectButton').removeClass('active');
      $('.case-list.selected').removeClass('selected');
    }
  }

  $(function(){
    clickOnCase();
  });

  function clickOnCase(){
    $('.case-list-holder').on('click','.case-list',function(e){
      if(!select){
          const url = $(this).data('href');
          window.location.href = url;
      }

      $(this).toggleClass('selected');
      const id = $(this).data('id');
      if($(this).hasClass('selected')){
        const input = '<input type="hidden" name="cases[]" value="' + id + '">';
        $('form[name="excel_export"]').append(input);
      }
      else{
        $('form[name="excel_export"] input[value="' + id + '"]').remove();
      }
    });
  }

</script>
@endsection

@section('content')
<div class="row mt-5">
  <div class="col-12">
    <h1>{{__('common.filter-results')}} ({{$cases->total()}}{{__('common.db')}})</h1>
  </div>
  <div class="col-12 button-holder">
    <div class="myBtn mr-0">
        <a class="button btn-radius d-flex" href="{{route('operator.cases.filter')}}">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            <span>{{__('common.new-filter')}}</span>
        </a>
    </div>
    <div class="myBtn mr-0">
      <form name="excel_export" method="post" action="export">
        {{csrf_field()}}
        <button class="button btn-radius d-flex" type="submit">
            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
            <span>
                {{__('common.save-excel')}}
            </span>
        </button>
      </form>
    </div>
    <div class="myBtn">
        <button class="button btn-radius d-flex" style="--btn-margin-right: 0px;" id="selectButton" onClick="selectClick()">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; margin-bottom:3px"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span>{{__('common.select-to-export')}}</span>
        </button>
    </div>
  </div>
  <div class="col-12 case-list-holder">
      @foreach($cases as $case)
        @component('components.cases.list',['case' => $case])@endcomponent
      @endforeach
  </div>
  {{$cases->appends(request()->all())->links()}}
</div>
@endsection
