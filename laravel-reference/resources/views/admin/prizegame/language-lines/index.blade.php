@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/translations.css?t={{time()}}">
@endsection

@section('extra_js')
    <script src="/assets/js/eap-online/translations.js?v={{time()}}" charset="utf-8"></script>
    <script>
        let iterator = 0;

        function addInput() {
            let html = '<form method="post" class="row w-100">{{csrf_field()}}\
            <div class="col-12 input mb-3">\
                      <div class="row">\
                        <div class="col-4 pl-0">\
                          <input name="new[' + iterator + '][key]"  required value="" placeholder="{{__('eap-online.system.input_name')}}">\
                        </div>\
                      </div>';

            @foreach($languages as $language)
                html += '<div class="row translation">\
                <div class="col-1 text-center" style="padding-top:15px;">\
                    {{$language->code}}\
                </div>\
                <div class="col-8 pl-0">\
                  <textarea name="new[' + iterator + '][text][{{$language->code}}]" placeholder="{{__('eap-online.system.translation')}}"></textarea>\
                </div>\
              </div>';
            @endforeach
                html += '<div class="col-12 mt- mb-4">\
                <button type="submit" class="button btn-radius d-flex align-items-center"><img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">{{__('common.save')}}</button>\
        </div></div></form>';
            iterator++;

            $('#holder').prepend(html);
        }

    </script>
    <script>
        getContent();

        $('#search').on('input', function (e) {
            getContent(e.target.value)
        });

        function resetSearch() {
            $("#search").val("");
            getContent();
        }

        function getContent(needle = '') {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: '/ajax/get-prizegame-translations-lines',
                data: {
                    needle
                },
                success: function (data) {
                    $('#holder').html(createHTML(data));
                },
            });
        }

        function createRowHTML(line, index, data) {
            let html = '';
            html += `
                <div class="col-12 input">
                    <div class="row">
                        <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                             onclick="toggleTranslationSection('system-${line.id}-translations', this)"
                        >
                            <p class="m-0 mr-3">${(line.text.hu ? line.text.hu : line.text.en)}</p>
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-wrap">`;

            data.languages.forEach((language) => {
                if (language.code in line.text) {
                    html += `
                                         <div style="background-color:rgb(145,183,82);" class="px-2 text-white mr-3 mb-2">
                                            ${language.code}
                                        </div>`;
                } else {
                    html += `
                                         <div style="background-color:rgb(219, 11, 32);" class="px-2 text-white mr-3 mb-2">
                                            ${language.code}
                                        </div>`;
                }
            });
            html += `</div><svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
</svg>
                            </div>
                            <input type="hidden" name="old[${index}][key]" required value="${line.key}"
                                   readonly>
                        </div>
                    </div>
                    <div class="d-none" id="system-${line.id}-translations">`;

            data.languages.forEach((language) => {
                html += `
                              <div class="row translation">
                                <div class="col-1 text-center" style="padding-top:15px;">
                                    ${language.code}
                                    </div>
                                    <div class="col-8 pl-0">
                                                    <textarea name="old[${index}][text][${language.code}]"
                                                          placeholder="{{__('eap-online.system.translation')}}">${(language.code in line.text) ? line.text[language.code] : ''}</textarea>
                                </div>
                              </div>`
            });
            html += `<div class="col-12 mt- mb-4">
                        <button type="submit" class="button btn-radius d-flex align-items-center"><img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">{{__('common.save')}}</button>
                        </div>
                    </div>
                </div>`;

            return html;
        }

        function createCategoryRowHTML(type, name) {
            return `
            <form method="post" class="row w-100">
            {{csrf_field()}}
            <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line d-flex" onClick="toggleTranslationSection('${type}', this)">
                    <div class=" d-flex justify-content-between align-items-center col-12">
                        <p class="m-0">${name}</p>
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
</svg>
                    </div>
                </div>

            <div class="col-12 input d-none category-row-lead" id="${type}">
            `;
        }

        function createHTML(data) {
            let html = '';
            let categories = {};

            data.translation_lines.forEach((line, index) => {
                switch (true) {
                    case (line.key.split('_').shift().includes('prizegame')):
                        if (!('prizegame' in categories)) {
                            categories['prizegame'] = createCategoryRowHTML('prizegame', '{{__('eap-online.system_translation_categories.prizegame')}}');
                            categories['prizegame'] += createRowHTML(line, index, data);
                        } else {
                            categories['prizegame'] += createRowHTML(line, index, data);
                        }
                        break;
                }
            });

            categories = Object.fromEntries(Object.entries(categories).sort());

            Object.values(categories).forEach((category) => {
                category += '</form></div>';
                html += category;
            })


            return html;
        }
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5 p-0">
            {{ Breadcrumbs::render('prizegame.translations.system') }}
            <h1>{{__('myeap.system.translations')}}</h1>
            <div class="w-100 d-flex align-items-center col-12 p-0">
                <button class="btn-radius" onClick="addInput()" style="--btn-height: auto; --btn-margin-bottom: 0px;">{{__('eap-online.system.new')}}</button>
                <input class="mb-0 btn-input-field-height" type="text" id="search" placeholder="{{__('eap-online.system.search')}}">
                <button class="flex-grow-1 btn-radius" onClick="resetSearch()"
                style="--btn-height: auto; --btn-margin-left: var(--btn-margin-x); --btn-margin-bottom: 0px;">
                    <div class="d-flex flex-row">
                        <img src="{{asset('assets/img/reset.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                        {{__('eap-online.system.reset')}}
                    </div>
                </button>
            </div>
        </div>
        <div class="col-12 pr-0" id="holder">

        </div>
        <div class="row col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.prizegame.actions') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
    </div>
@endsection
