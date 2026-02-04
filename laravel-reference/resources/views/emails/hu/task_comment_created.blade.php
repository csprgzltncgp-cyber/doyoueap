<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
    <p>Kedves {{$reciever->name}}!</p>
    <p><strong>{{$sender->name}}</strong> új üzentet küldött az alábbi feladattal kapcsolatban!</p>

    <p>Azonosító: <strong>{{$task->id}}</strong></p>
    <p>Határidő: <strong>{{\Carbon\Carbon::parse($task->deadline)->format('Y-m-d')}}</strong></p>

    <p>A részletek megtekintéséhez kattints az alábbi linkre:</p>

    @if($task->from_id == $reciever->id)
        <a href="{{route( $reciever->type . '.todo.edit', ['task' => $task])}}">{{route( $reciever->type . '.todo.edit', ['task' => $task])}}</a>
    @else
        <a href="{{route( $reciever->type . '.todo.show', ['task' => $task])}}">{{route( $reciever->type . '.todo.show', ['task' => $task])}}</a>
    @endif

    <p>Üdvözlettel,
        <br/>
        EAP Team
    </p>
</body>
</html>
