<table>
    <thead>
    <tr>
        <th>Id</th>
        @foreach($data[0]['answers'] as $answer_group)
            @foreach($answer_group as $answer_key => $answer_value)
                <th>{{ucwords($answer_key)}}</th>
            @endforeach
        @endforeach
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $user_answer)
        <tr>
            <td>{{$user_answer['id']}}</td>
            @foreach($user_answer['answers'] as $answer_group)
                @foreach($answer_group as $answer_key => $answer_value)
                    <td>{{$answer_value}}</td>
                @endforeach
            @endforeach
            <td>{{$user_answer['date']}}</td>
        </tr>
    @endforeach
    </tbody>
    <tbody>
</table>