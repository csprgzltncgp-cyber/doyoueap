<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
    <p>Kedves {{$task->from->name}}!</p>

    <p><strong>{{$task->to->name}}</strong> befejzete az alábbi feladatot!</p>

    <p>Azonosító: <strong>{{$task->id}}</strong></p>
    <p>Határidő: <strong>{{\Carbon\Carbon::parse($task->deadline)->format('Y-m-d')}}</strong></p>

    <p>Üdvözlettel,
        <br/>
        EAP Team
    </p>
</body>
</html>
