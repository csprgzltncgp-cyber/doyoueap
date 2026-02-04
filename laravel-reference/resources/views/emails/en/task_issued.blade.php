<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Dear {{$task->to->name}}!</p>

<p><strong>{{$task->from->name}}</strong> has assigned a new task to you!</p>

<p>Identifier: <strong>{{$task->id}}</strong></p>
<p>Due date: <strong>{{\Carbon\Carbon::parse($task->deadline)->format('Y-m-d')}}</strong></p>

<p>To see the details, please click on the link below:</p>
<a href="{{route( $task->to->type . '.todo.show', ['task' => $task])}}">{{route( $task->to->type . '.todo.show', ['task' => $task])}}</a>

<p>Best regards,
    <br/>
    EAP Team
</p>
</body>
</html>
