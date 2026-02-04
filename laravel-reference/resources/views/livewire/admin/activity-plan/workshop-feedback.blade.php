<div id="workshop_feedback_container">
    <div class="mt-5 fix-activity">
        <div class="header">
            <h2>{{__('common.workshop_feedback')}}</h2>
        </div>

        <div class="mt-3">
            @foreach ($workshops as $index => $workshop)
                <x-workshop-feedback.list-item
                    :workshop="$workshop"
                    :index="$index"
                />
            @endforeach

            @if(!$workshops->count())
                <center>{{__('data.no_data')}}</center>
            @endif
        </div>
    </div>
</div>
