<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Dear {{$operator->name}}!</p>

<p>You have received a request.</p>
<p>Click this link to see the case log:<a
            href="https://operatordashboard.chestnutce.com/operator/cases/{{$case->id}}">https://operatordashboard.chestnutce.com/operator/cases/{{$case->id}}</a>
</p>

<p>Sender: {{$user->name}}, {{$user->email}}</p>

<p>Message from expert:</p>

<p>{{$question}}</p>

<p>Best regards,<br/>
    EAP Team</p>
</body>
</html>
