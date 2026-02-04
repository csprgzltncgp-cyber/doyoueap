<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
    <p>Kedves {{$affiliateSearch->from->name}}!</p>

    <p><strong>{{$affiliateSearch->to->name}}</strong> befejzete az alábbi szakértőkeresési feladatot feladatot!</p>

    <p>Azonosító: <strong>{{$affiliateSearch->id}}</strong></p>
    <p>Határidő: <strong>{{\Carbon\Carbon::parse($affiliateSearch->deadline)->format('Y-m-d')}}</strong></p>

    <p>Üdvözlettel,
        <br/>
        EAP Team
    </p>
</body>
</html>
