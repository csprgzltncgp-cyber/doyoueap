<table>
    <thead>
    <tr>
        <th>{{__('prizegame.lottery.serial_number')}}</th>
        <th>{{__('prizegame.lottery.username')}}</th>
        <th>{{__('prizegame.lottery.email')}}</th>
        <th>{{__('prizegame.lottery.date')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($winners as $winner)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ $winner->guess->username }}</td>
            <td>{{ $winner->guess->email }}</td>
            <td>{{ $winner->guess->created_at}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
