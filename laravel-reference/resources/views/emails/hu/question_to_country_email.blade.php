<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Kedves {{$operator->name}}!</p>

<p>Kérdés érkezett számodra a következő esettel kapcsolatban.</p>
<p>Az esetlap megnyitásához kattints erre a linkre: <a
            href="https://operatordashboard.chestnutce.com/operator/cases/{{$case->id}}">https://operatordashboard.chestnutce.com/operator/cases/{{$case->id}}</a>
</p>

<p>Feladója: {{$user->name}}, {{$user->email}}</p>

<p>A szakértő által küldött üzenet:</p>

<p>{{$question}}</p>

<p>Üdvözlettel,</br>
    CGP Europe</p>
</body>
</html>