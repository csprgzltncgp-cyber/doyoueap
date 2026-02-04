<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Dear {{$reciever->name}}!</p>
<p><strong>{{$sender->name}}</strong> has sent a new message about the following task!</p>

<p>Identifier: <strong>{{$task->id}}</strong></p>
<p>Due date: <strong>{{\Carbon\Carbon::parse($task->deadline)->format('Y-m-d')}}</strong></p>

<p>To see the details, please click on the link below:</p>

@if($task->from_id == $reciever->id)
    <a href="{{route( $reciever->type . '.todo.edit', ['task' => $task])}}">{{route( $reciever->type . '.todo.edit', ['task' => $task])}}</a>
@else
    <a href="{{route( $reciever->type . '.todo.show', ['task' => $task])}}">{{route( $reciever->type . '.todo.show', ['task' => $task])}}</a>
@endif

<p>Best regards,
    <br/>
    EAP Team
</p>
</body>
</html>
