<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Kedves {{$operator->name}}!</p>

<p>E-mail üzenet érkezett számodra a {{$country->email}} e-mail címre.</p>
<p>Az esetlap megnyitásához klikkelj erre a linkre: <a
            href="https://operatordashboard.chestnutce.com/operator/cases/{{$case->id}}">https://operatordashboard.chestnutce.com/operator/cases/{{$case->id}}</a>
</p>

<p>Az e-mail
    tárgya: {{\Carbon\Carbon::parse($case->created_at)->format('Y-m-d') . ' - ' . ($case->company ? $case->company->name : '' ) .  ' - ' . $operator->name }}</p>

<p>Feladója: {{$user->email}}</p>

<p>Kérlek, mielőbb lépj be {{$country->email}} e-mail cím postafiókjába és küldj onnan válaszüzenetet a szakértőnek.</p>

<p>Üdvözlettel,<br/>
    EAP Team</p>
</body>
</html>
