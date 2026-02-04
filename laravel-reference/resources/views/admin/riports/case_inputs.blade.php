@extends('layout.master')

@section('title')
Admin Dashboard
@endsection

@section('extra_css')
  <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
  <style>
    .select-holder{
      position: absolute;
      right:10px;
      top:50%;
      transform: translateY(-50%);
    }

    .select-holder *{
      margin-bottom:0px;
    }

    .select-holder label{
      margin-left:20px;
    }

    .list-element{
      position: relative;
    }
  </style>
@endsection

@section('extra_js')
  <script>
    $(function(){
      displayFormatChangeHandler();
      chartChangeHandler();
    })

    function ajaxCall(value, inputId, type){
      $.ajax({
        headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        type:'POST',
        url:'/ajax/set-riport-input',
        data : {
          value : value,
          inputId : inputId,
          type : type
        },
        success: function(data){
          if(data.status == 0){
            Swal.fire(
              '{{__('common.edit-successful')}}',
              '',
              'success'
            );
          }
          else
          {
            Swal.fire(
              'Az módosítás sikertelen!',
              '',
              'error'
            );
          }
        },
        error: function(error){
          Swal.fire(
            'Az módosítás sikertelen!',
            'SERVER ERROR!',
            'error'
          );
        }
      });
    }

    function displayFormatChangeHandler(){
      $('select[name="display_format"]').change(function(){
        const value = $(this).val();
        const input_id = $(this).closest('.list-element').data('id');
        const type = 'display_format';
        ajaxCall(value, input_id, type);
      });
    }

    function chartChangeHandler(){
      $('select[name="chart"]').change(function(){
        const value = $(this).val();
        const input_id = $(this).closest('.list-element').data('id');
        const type = 'chart';
        ajaxCall(value, input_id, type);
      });
    }
  </script>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <h1>Inputok listája</h1>
    @foreach($caseInputs as $caseInput)
      <div class="list-element col-12 " data-id = "{{$caseInput->id}}">
        {{$caseInput->name}}{{$caseInput->company ? ' - '.$caseInput->company->name : ''}}

        <div class="select-holder">
          <label>Megjelenítés:</label>
          <select name="display_format">
              <option @if($caseInput->display_format == 'icon') selected @endif value="icon">Ikon</option>
              <option @if($caseInput->display_format == 'table') selected @endif value="table">Táblázat</option>
          </select>
          <label>Diagram:</label>
          <select name="chart">
              <option @if($caseInput->chart == 1) selected @endif value="1">Igen</option>
              <option @if($caseInput->chart == 0) selected @endif value="0">Nem</option>
          </select>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
