@extends('layout.master')

@section('title')
Operator Dashboard
@endsection

@section('extra_css')
<link rel="stylesheet" href="/assets/css/cases/new.css?t={{time()}}">
<link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
@endsection

@section('extra_js')
<script src="/assets/js/datetime.js" charset="utf-8"></script>
<script>
  var company_id;
  var city_case_input_id = {{$case_inputs->firstWhere('default_type','location')->id}};

  $(function(){
    var width = $('.select-button').outerWidth() != 0 ? $('.select-button').outerWidth() : $('.new-case-buttons .steps:first-child input').outerWidth();
    $('.select-list').css('width',width);
    $('.select-list').css('margin-left',$('.back-button').outerWidth() + 4);
    editCurrentStepNumber();
    selectFromList();

    companyChangeHandler();
    permissionChangeHandler();

    $('.datepicker').datepicker({
      'format' : 'yyyy-mm-dd'
    });
  });

  function companyChangeHandler(){
    $('input#company_chooser').on('change',function(){
        $('#permissions .permission').remove();
        $('#permissions #loading').remove();
        $('#permissions').append('<p id="loading">{{__("common.loading")}}...</p>');
        company_id = $(this).val();
        //le kell kérni a cég inputjait és jogosultságaits
         $.ajax({
           headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
           type:'POST',
           url:'/ajax/get-company-permissions-and-steps',
           data:{
             id : company_id,
           },
           success:function(data){
              if(data.status == 0){
                //jogosultságok kiírása
                console.log(data);
                $('#permissions #loading').remove();
                $.each(data.permissions, function(index,value){
                  const html = '<div class="permission">\
                      <p>' + value.slug + '</p>\
                      <p>' + value.pivot.contact + '</p>\
                      <p>' + value.pivot.number + ' alkalom</p>\
                  </div>';
                  $('#permissions').append(html);
                });

                //step-ek hozzáadása
                $.each(data.steps,function(index,value){
                  var key = $('.new-case-buttons .steps:last-child()').data('step');
                  key += 1;
                  let html = '<div class="steps d-none" data-step = "'+ key +'" data-type="'+ value.type +'">\
                      <button type="button" name="button" class="back-button"  onClick="backButton(this)">{{__("common.back")}}</button>';

                      if(value.type == 'select'){
                        html += '<button type="button" name="button" class="select-button" data-type="'+ value.type +'" onClick=\'actionButton(this)\'>'+ value.name +'</button>\
                          <input type="hidden" name="inputs['+ value.id +']" >';
                      }else if(value.type =='date'){
                          html += '<input type="text" name="inputs['+ value.id +']" class="datepicker" placeholder="'+ value.name +'"/>';
                      }else if(value.type == 'integer'){
                          html += '<input type="number" name="inputs['+ value.id +']" placeholder="'+ value.name +'"/>';
                      }else if(value.type == 'double'){
                          html += '<input type="text" name="inputs['+ value.id +']" placeholder="'+ value.name +'"/>';
                      }else if(value.type == 'text'){
                          html += '<input type="text" name="inputs['+ value.id +']" placeholder="'+ value.name +'"/>';
                      }

                    html += '<button type="button" name="button" class="next-button" onClick="nextButton(this)" data-defaulttype="'+ value.default_type +'">{{__("common.forward")}}</button>\
                      </div>\
                    </div>';
                    $('.new-case-buttons').append(html);
                });
              }
            }
        });
    });
  }

  function permissionChangeHandler(){
    $('input#case_type').on('change',function(){
      $('#loading').remove();
      $('#experts .expert').remove();
      $('#experts').append('<p id="loading">{{__("common.loading")}}...</p>');
      var permission_id = $(this).prev('input').val();
      $.ajax({
        headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        type:'POST',
        url:'/ajax/get-available-expert-by-permission',
        data:{
          permission_id : permission_id,
          city_id : $('input[name="inputs['+ city_case_input_id +']"]').val()
        },
        success:function(data){
          $('#loading').remove();
          $('.expert').remove();
          $.each(data.experts, function(index,value){
            const html = '<div class="expert">\
                <span>' + value.name + '</span>\
                <span>{{__("common.send-mail")}}</span>\
            </div>';
            $('#experts').append(html);
          });
        }
      });
    });
  }

  function selectFromList(){
    $('.new-case-buttons').on('click','ul.select-list li',function(){
      const li = $(this);
      const steps = li.closest('.steps');
      const id = li.data('id');
      const text = li.html();
      steps.find('button.select-button').html(text);
      steps.find('input[type="hidden"]').val(id);
      steps.find('input[type="hidden"]').trigger('change');
      steps.find('.select-list').css('display','none');
    });
  }

  function nextButton(element){
    var current_step = $(element).closest('.steps');

    if(current_step.find('input[name^="inputs["]').val() == ''){
        $('#required').removeClass('d-none');
        return false;
    }
    $('#required').addClass('d-none');
    const next_step = current_step.next('.steps');

    if(next_step.length > 0){
      current_step.removeClass('d-block').addClass('d-none');
      current_step.find('.select-list').css('display','none');
      next_step.addClass('d-block').removeClass('d-none');
      next_step.trigger('show');
    }else{
      $('form').submit();
    }
  }

  function backButton(element){
    var current_step = $(element).closest('.steps');
    var prev_step = current_step.prev('.steps');
    if(prev_step.length > 0){
      current_step.removeClass('d-block').addClass('d-none');
      current_step.find('.select-list').css('display','none');
      prev_step.addClass('d-block').removeClass('d-none');
      prev_step.trigger('show');
    }
  }

  function editCurrentStepNumber(){
    $('.new-case-buttons').on('show','.steps',function(){
      var key = $(this).data('step');
      $('#current-step').html(key);
    });
  }

  function actionButton(element){
    const type = $(element).data('type');
    if(type == 'select'){
      $(element).closest('.steps').find('ul.select-list').show();
    }else if(type == 'date'){
      $(element).closest('.steps').find('input[type="hidden"]').trigger('click');
    }
  }

</script>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <h1>#{{$case->id}} eset módosítása - <span id="current-step">1</span><span id="all-steps">/17</span></h1>
  </div>
  <form method="post" class="col-8">
    {{csrf_field()}}
    <div class="new-case-buttons">
      @foreach($case_inputs as $key => $case_input)
        <div class="steps @if($key == 0) d-block @else d-none @endif" data-step = "{{$key+1}}" data-type="{{$case_input->type}}">
          <button type="button" name="button" class="back-button"  onClick="backButton(this)">Vissza</button>
          @if($case_input->default_type == 'company_chooser')
            <button type="button" name="button" class="select-button" data-type="{{$case_input->type}}" onClick='actionButton(this)'>
              {{ $case->company->name }}
            </button>
            <input type="hidden" id="company_chooser" name="inputs[{{$case_input->id}}]" value="{{$case->values->firstWhere('case_input_id',$case_input->id)->value}}">
          @elseif($case_input->default_type == 'case_type')
            <button type="button" name="button" class="select-button" data-type="{{$case_input->type}}" onClick='actionButton(this)'>
              {{$case->values->firstWhere('case_input_id',$case_input->id)->getValue()}}
            </button>
            <input type="hidden" id="case_type" name="inputs[{{$case_input->id}}]" value="{{$case->values->firstWhere('case_input_id',$case_input->id)->value}}">
          @elseif($case_input->type == 'select')
              <button type="button" name="button" class="select-button" data-type="{{$case_input->type}}" onClick='actionButton(this)'>
                {{$case->values->firstWhere('case_input_id',$case_input->id)->getValue()}}
              </button>
              <input type="hidden" name="inputs[{{$case_input->id}}]" value="{{$case->values->firstWhere('case_input_id',$case_input->id)->value}}" >
          @elseif($case_input->type == 'date')
            <input type="text" name="inputs[{{$case_input->id}}]" class="datepicker" placeholder="{{$case_input->translation->value}}" value="{{$case->values->firstWhere('case_input_id',$case_input->id)->value}}"/>
          @elseif($case_input->type == 'integer')
            <input type="number" name="inputs[{{$case_input->id}}]" placeholder="{{$case_input->translation->value}}" value="{{$case->values->firstWhere('case_input_id',$case_input->id)->value}}"/>
          @elseif($case_input->type == 'double')
            <input type="text" name="inputs[{{$case_input->id}}]" placeholder="{{$case_input->translation->value}}" value="{{$case->values->firstWhere('case_input_id',$case_input->id)->value}}"/>
          @elseif($case_input->type == 'text')
            <input type="text" name="inputs[{{$case_input->id}}]" placeholder="{{$case_input->translation->value}}" value="{{$case->values->firstWhere('case_input_id',$case_input->id)->value}}"/>
          @endif
          <button type="button" name="button" class="next-button" onClick="nextButton(this)" data-defaulttype="{{$case_input->default_type}}">Tovább</button>
            @if($case_input->type == 'select')
              <ul class="select-list">
                @if($case_input->default_type == 'company_chooser')
                    <li data-id="{{$case->company->id}}">{{$case->company->name}}</li>
                @elseif($case_input->default_type == 'case_type')
                  @foreach($permissions as $permission)
                    <li data-id="{{$permission->id}}">{{$permission->translation->value}}</li>
                  @endforeach
                  @elseif($case_input->default_type == 'location')
                    @foreach($cities as $city)
                      <li data-id="{{$city->id}}">{{$city->name}}</li>
                    @endforeach
                @else
                  @foreach($case_input->values as $case_input_value)
                    @if($case_input_value->translation)
                      <li data-id="{{$case_input_value->id}}">{{$case_input_value->translation->value}}</li>
                    @endif
                  @endforeach
                @endif
              </ul>
            @endif
        </div>
      @endforeach
    </div>
    <div id="required" class="d-none">
      <p>A mező kitöltése kötelező! Válassz a legördülő menüből!</p>
    </div>
  </form>
  <div class="col-4">
    <div id="permissions" class="right-side">
        <p class="title">{{__('common.authorizations')}}:</p>
    </div>
    <div id="experts" class="right-side">
      <p class="title">{{__('common.available-experts')}}:</p>
    </div>
  </div>
</div>
@endsection
