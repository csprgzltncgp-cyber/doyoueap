@props([
    'newTasks',
    'unreadTaskComments',
])

<li><a href="{{route('production_translating_admin.submenu.digital')}}">{{__('common.digital')}}</a></li>
@if(auth()->user()->connected_account == null)
    <li class='d-flex justify-content-between align-items-center'>
        <a href="{{route('production_translating_admin.dashboard')}}">TODO</a>
        <li><a href="{{route('production_translating_admin.assets.menu')}}">{{__('common.assets')}}</a></li>
        <div>
            @if(!empty($newTasks) && $newTasks > 0)
                <span class="ml-2" style="width: 36px; height: 36px; padding: 8px; text-align: center; background: rgb(145, 183, 82); border-radius: 50%; float: right; color: white"
                    class="unread_counter">{{$newTasks}}</span>
            @endif

            @if($unreadTaskComments)
                <span class="ml-2" style="width: 36px; height: 36px; padding: 8px; text-align: center; background: #007bff; border-radius: 50%; float: right; color: white"
                    class="unread_counter mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px" class="mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </span>
            @endif
        </div>
    </li>
@endif
