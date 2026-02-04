<ul class="border-0 m-0 pb-0 pt-0 pl-0" style="color: black !important; list-style: none">
    @foreach($childs as $child)
        <li>
            <label class="container checkbox-container pb-2"
                   id="customer-satisfaction-not-possible">{{$child->name}}
                <input type="radio" name="categories[]{{$type}}" value="{{$child->id}}"
                       @if(isset($resource) && $resource->hasCategory($child->id)) checked @endif
                       @if(!empty(old('categories')) && in_array($child->id , old('categories'))) checked @endif
                >
                <span class="checkmark" style="opacity: {{1 - (($level / 10) * 2.5)}}"></span>
            </label>
            @if(count($child->childs))
                @if(!isset($resource))
                    @include('components.eap-online.category-line',['childs' => $child->childs, 'level' => $level + 1, 'type' => $type])
                @else
                    @include('components.eap-online.category-line',['childs' => $child->childs, 'level' => $level + 1, 'resource' => $resource, 'type' => $type])
                @endif
            @endif
        </li>
    @endforeach
</ul>