<div>

    <p>Hello!</p>

    <p>
        This email was sent on {{\Carbon\Carbon::now()->format('Y-m-d H:i')}}.
        <br>The target date was  {{\Carbon\Carbon::now()->setDay($day)->setTime(8,0)->format('Y-m-d H:i')}}
        @if (\Carbon\Carbon::now()->setDay($day)->isWeekend())
            <br>The target date was a weekend day.
        @else
            <br><br>The target date was not a weekend.
        @endif
    </p>

    Best regards,<br>
    CGP Europe
</div>