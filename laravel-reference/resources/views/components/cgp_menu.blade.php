<div class="row menu-row" style="font-family: CalibriB;">
    <div class="col-12">
        <div id="menu" style="margin-right: 15px;">
            <div class="d-flex align-items-center" onClick="showHideMenu()">
                <button type="button" class="m-0 menu-btn" style="display: block;" id="menu-button" name="button">
                    <div class="row justify-content-center align-items-center">
                        <span>{{__('common.menu')}}</span>
                    </div>
                </button>
            </div>
            <div id="menu-list-holder" style="display:none;">
                <button type="button" class="menu-btn" style="margin-bottom: 16px !important;" id="fake-menu-button" name="button" onClick="showHideMenu()">
                    <div class="row justify-content-center align-items-center">
                        <span>{{__('common.menu')}}</span>
                    </div>
                </button>
                <ul>
                    @php
                        $unread_feedbacks = \App\Models\Feedback\Feedback::query()->whereNull('viewed_at')->get()->count();

                        $new_tasks = \App\Models\Task::query()->where('to_id', auth()->id())->where('status', \App\Models\Task::STATUS_CREATED)->get()->count();
                        $unread_task_comments = \App\Models\Task::query()->with('comments')->where('to_id', auth()->id())->orWhere('from_id', auth()->id())->get()->map(function($task){
                            return $task->has_new_comments();
                        })->sum();

                        $new_affiliate_searches = \App\Models\AffiliateSearch::query()->where('to_id', auth()->id())->where('status', \App\Models\AffiliateSearch::STATUS_CREATED)->get()->count();
                        $unread_affiliate_search_comments = \App\Models\AffiliateSearch::query()->with('comments')->where('to_id', auth()->id())->orWhere('from_id', auth()->id())->get()->map(function($affiliate_search){
                            return $affiliate_search->has_new_comments();
                        })->sum();
                    @endphp

                    @if(Auth::user()->type == 'admin')
                        <x-menus.admin
                            :unreadFeedbacks="$unread_feedbacks"
                            :newTasks="$new_tasks"
                            :unreadTaskComments="$unread_task_comments"
                            :newAffiliateSearches="$new_affiliate_searches"
                            :unreadAffiliateSearchComments="$unread_affiliate_search_comments"
                        />
                    @elseif(Auth::user()->type == 'account_admin')
                        <x-menus.account_admin
                            :newTasks="$new_tasks"
                            :unreadTaskComments="$unread_task_comments"
                            :newAffiliateSearches="$new_affiliate_searches"
                            :unreadAffiliateSearchComments="$unread_affiliate_search_comments"
                        />
                    @elseif(Auth::user()->type == 'eap_admin')
                        <x-menus.eap_admin
                            :unreadFeedbacks="$unread_feedbacks"
                            :newTasks="$new_tasks"
                            :unreadTaskComments="$unread_task_comments"
                            :newAffiliateSearches="$new_affiliate_searches"
                            :unreadAffiliateSearchComments="$unread_affiliate_search_comments"
                        />
                    @elseif(Auth::user()->type == 'production_admin')
                        <x-menus.production_admin
                            :newTasks="$new_tasks"
                            :unreadTaskComments="$unread_task_comments"
                        />
                    @elseif(Auth::user()->type == 'production_translating_admin')
                        <x-menus.production_translating_admin
                            :newTasks="$new_tasks"
                            :unreadTaskComments="$unread_task_comments"
                        />
                    @elseif(Auth::user()->type == 'todo_admin')
                        <x-menus.todo_admin
                            :newTasks="$new_tasks"
                            :unreadTaskComments="$unread_task_comments"
                        />
                    @elseif(Auth::user()->type == 'affiliate_search_admin')
                        <x-menus.affiliate_search_admin
                            :newAffiliateSearches="$new_affiliate_searches"
                            :unreadAffiliateSearchComments="$unread_affiliate_search_comments"
                        />
                    @elseif(Auth::user()->type == 'financial_admin')
                        <x-menus.financial_admin
                            :newTasks="$new_tasks"
                            :unreadTaskComments="$unread_task_comments"
                        />
                    @elseif(Auth::user()->type == 'supervisor_admin')
                        <x-menus.supervisor_admin/>
                    @elseif(Auth::user()->type == 'operator')
                        <x-menus.operator/>
                    @elseif(Auth::user()->type == 'expert')
                        <x-menus.expert/>
                    @endif

                    @foreach($menu as $m)
                        @php $action = \Illuminate\Support\Facades\Auth::user()->type == 'admin' ? 'edit': 'view'; @endphp

                        <li><a href="{{route(\Auth::user()->type.'.documents.'.$action,['id' => $m->id])}}">{{$m->name}}</a></li>
                    @endforeach

                    <li><a href="{{route(\Auth::user()->type.'.dashboard')}}">{{__('common.back-to-homepage')}}</a></li>

                    @if(session('myAdminId') && !in_array(Auth::user()->type, ['admin', 'production_admin', 'production_translating_admin', 'account_admin', 'financial_admin', 'eap_admin', 'affiliate_search_admin']))
                        <li><a style="cursor: pointer" id="backToAdmin" onClick="backToAdmin()">{{__('common.back-to-admin')}}</a></li>
                    @endif

                    <li><a href="{{route('logout')}}">{{__('common.logout')}}</a></li>
                </ul>
                <div class="row justify-content-center align-items-center" style="margin-top: 30px;">
                    <button id="arrow" style="padding-bottom:9px;" onClick="showHideMenu()">
                        <div class="row justify-content-center align-items-center">
                            <svg class="fa-rotate-180" xmlns="http://www.w3.org/2000/svg" style="height: 20px" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
