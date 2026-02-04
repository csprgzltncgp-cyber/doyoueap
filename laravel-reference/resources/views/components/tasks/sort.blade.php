@props([
    'id' => 'orderBySelect',
])

<div style="color: rgb(89, 198, 198)">
    <div class="d-flex pb-2">
        <span class="d-flex mr-1 align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
              </svg>
            {{__('common.sort_by')}}:
        </span>
        <select wire:ignore id="{{$id}}" class="sortSelect">
            <option value="deadline,desc">{{__('task.deadline')}} - {{__('task.descending')}}</option>
            <option value="deadline,asc">{{__('task.deadline')}} - {{__('task.ascending')}}</option>
            <option value="id,desc">{{__('common.identifier')}} - {{__('task.descending')}}</option>
            <option value="id,asc">{{__('common.identifier')}} - {{__('task.ascending')}}</option>
            <option value="users.name,asc">{{__('task.colleague')}}</option>
            <option value="status,asc">{{__('common.status')}}</option>
        </select>
    </div>
</div>
