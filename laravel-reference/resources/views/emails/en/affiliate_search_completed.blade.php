<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
    <p>Dear {{$affiliateSearch->from->name}}!</p>

    <p><strong>{{$affiliateSearch->to->name}}</strong> has completed the following affiliate searching task!</p>

    <p>Identifier: <strong>{{$affiliateSearch->id}}</strong></p>
    <p>Due date: <strong>{{\Carbon\Carbon::parse($affiliateSearch->deadline)->format('Y-m-d')}}</strong></p>

    <p>Best regards,
        <br/>
        EAP Team
    </p>
</body>
</html>
