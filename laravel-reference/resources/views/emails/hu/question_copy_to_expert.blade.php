<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Tisztelt {{$user->name}}!</p>

<p>E-mail üzenetét megkaptuk, munkatársunk hamarosan
    válaszüzenetet küldd az Ön e-mail címére: {{$user->email}}</p>

<p>Az Ön által küldött e-mail szövege:</p>

<p>{{$question}}</p>

<p>Üdvözlettel,<br/>
    EAP Team</p>
</body>
</html>
