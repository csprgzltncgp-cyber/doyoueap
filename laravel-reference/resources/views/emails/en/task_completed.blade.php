<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
    <p>Dear {{$task->from->name}}!</p>

    <p><strong>{{$task->to->name}}</strong> has completed the following task!</p>

    <p>Identifier: <strong>{{$task->id}}</strong></p>
    <p>Due date: <strong>{{\Carbon\Carbon::parse($task->deadline)->format('Y-m-d')}}</strong></p>

    <p>Best regards,
        <br/>
        EAP Team
    </p>
</body>
</html>
