@extends('layout.master')

@section('extra_css')
<link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
<link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
<style media="screen">
  form{
    max-width: none;
  }
</style>
@endsection

@section('extra_js')
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>
<script>
    const editor = SUNEDITOR.create(document.getElementById('editor'), {
        height: 300,
    });

    document.querySelector('form').addEventListener('submit', function(e){
        editor.save();
    });
</script>
@endsection

@section('title')
Admin Dashboard
@endsection

@section('content')
{{ Breadcrumbs::render('documents.edit', $document) }}
<h1>{{$document->name}}</h1>
<form  method="post" class="row">
    {{csrf_field()}}
    <div class="col-12 col-sm-12">
      <div class="form-group">
        <label for="">Név</label>
        <input type="text" name="name" value="{{$document->name}}" placeholder="Név" required>
      </div>
      <div class="form-group">
        <label for="">Ki láthatja?</label>
        <label class="checkbox-container">Admin
            <input type="checkbox" name="visible[]" value="admin" @if(strpos($document->visibility, 'admin') !== FALSE) checked="checked" @endif >
            <span class="checkmark"></span>
        </label>
        <label class="checkbox-container">Expert
            <input type="checkbox" name="visible[]" value="expert" @if(strpos($document->visibility, 'expert') !== FALSE) checked="checked" @endif>
            <span class="checkmark"></span>
        </label>
        <label class="checkbox-container">Operator
            <input type="checkbox" name="visible[]" value="operator" @if(strpos($document->visibility, 'operator') !== FALSE) checked="checked" @endif>
            <span class="checkmark"></span>
        </label>
      </div>
      <div class="form-group">
        <label for="">Ország</label>
        <select name="country_id" required>
            @foreach($countries as $country)
              <option value="{{$country->id}}" @if($document->country_id == $country->id) selected @endif>{{$country->code}}</option>
            @endforeach
        </select>
      </div>
      <div class="form-group">
        <label for="">Nyelv</label>
        <select name="language_id" required>
            @foreach($languages as $language)
              <option value="{{$language->id}}" @if($document->language_id == $language->id) selected @endif>{{$language->name}}</option>
            @endforeach
        </select>
      </div>
    </div>
    <div class="col-12 col-sm-12">
      <div class="form-group">
        <label for="">Szöveg</label>
        <textarea name="text" id="editor">{{$document->text}}</textarea>
      </div>
    </div>
    <div class="col-12">
      <div class="form-group">
        <button type="submit" class="button btn-radius d-flex justify-content-center align-items-center" name="button"
        style="--btn-max-width: var(--btn-min-width); --btn-margin-right: 0px;">
            <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
            {{__('common.save')}}
        </button>
      </div>
    </div>
  </div>
</form>
@endsection
