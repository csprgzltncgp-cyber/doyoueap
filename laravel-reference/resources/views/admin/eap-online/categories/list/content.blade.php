@extends('admin.eap-online.categories.list.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script>
        let indexForNewCategories = 0;

        function newCategorySection() {


            const html = `
                        <div class="row col-12 d-flex">
                         <select name="new_categories[${indexForNewCategories}][language_id]" class="w-25 mr-5">
                           @foreach($languages as $language)
            <option value="{{$language->id}}">{{$language->name}}</option>
                            @endforeach
            </select>
            @if($type == 'self-help')
            <select name="new_categories[${indexForNewCategories}][parent_id]" class="w-25 mr-5">
                            <option value="null">No parent category</option>
                @foreach ($parent_categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>

                @if (count($category->childs) > 0)
            @include('components.eap-online.subcategories_component', ['subcategories' => $category->childs, 'parent' => $category->name, 'level' => 1])
            @endif

            @endforeach
            </select>
            @endif
            <input class="w-25" type="text" name="new_categories[${indexForNewCategories}][name]" required>
                                                    </div>
        `;
            $('#categories_holder').append(html);
            indexForNewCategories++;
        }
    </script>
@endsection

@section('category_list')
    <div class="row col-12">
        <div class="col-12 d-flex">
            <h1 class="w-25 mr-5">{{__('eap-online.actions.language')}}</h1>
            @if($type == 'self-help')
                <h1 class="w-25 mr-5">{{__('eap-online.categories.parent')}}</h1>
            @endif
            <h1 class="w-25">{{__('eap-online.categories.name')}}</h1>
        </div>
        <div class="col-12">
            <form class="mw-100" action="{{route('admin.eap-online.categories.update')}}" method="post">
                {{csrf_field()}}
                <div id="categories_holder">
                    @foreach($categories as $category)
                        @component('components.eap-online.category_line_component',['current_category' => $category, 'categories' => $categories, 'index' => $loop->index, 'type' => $type])@endcomponent
                    @endforeach
                </div>
                <div class="row col-12 d-flex">
                    <div class="mr-3">
                        <button class="text-center btn-radius" style="--btn-margin-right: 0px;" type="button" onclick="newCategorySection()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width: 20px" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>
                                {{__('common.add')}}
                            </span>
                        </button>
                    </div>
                    <div>
                        <button class="text-center button btn-radius" type="submit">
                            <img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="mt-1">
                                {{__('common.save')}}
                            </span>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="type" value="{{$type}}">
            </form>
        </div>
    </div>
@endsection
