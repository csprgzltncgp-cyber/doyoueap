<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Dear {{$operator->name}}!</p>

<p>You have received a message on the {{$country->email}} email address.</p>
<p>Click this link to see the case log: <a
            href="https://operatordashboard.chestnutce.com/operator/cases/{{$case->id}}">https://operatordashboard.chestnutce.com/operator/cases/{{$case->id}}</a>
</p>

<p>Email
    subject: {{\Carbon\Carbon::parse($case->created_at)->format('Y-m-d') . ' - ' . ($case->company ? $case->company->name : '' ) . ' - ' . $operator->name }}</p>

<p>Sender: {{$user->email}}</p>

<p>Please sign in to the {{$country->email}} email account to reply to the expert.</p>

<p>Best regards,<br/>
    EAP Team</p>
</body>
</html>
