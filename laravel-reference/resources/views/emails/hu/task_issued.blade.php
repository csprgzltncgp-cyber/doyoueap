<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Kedves {{$task->to->name}}!</p>

<p><strong>{{$task->from->name}}</strong> új feladatot rendelt hozzád!</p>

<p>Azonosító: <strong>{{$task->id}}</strong></p>
<p>Határidő: <strong>{{\Carbon\Carbon::parse($task->deadline)->format('Y-m-d')}}</strong></p>

<p>A részletek megtekintéséhez kattints az alábbi linkre:</p>
<a href="{{route( $task->to->type . '.todo.show', ['task' => $task])}}">{{route( $task->to->type . '.todo.show', ['task' => $task])}}</a>

<p>Üdvözlettel,<br/>
    EAP Team</p>
</body>
</html>
