<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Hello {{$user->name}}!</p>

<p>You are receiving this email because we received a password reset request for your account.</p>

<p>Your new password is: {{$password}}</p>

<p>Now you can login to your account with your new password at this link: <a
        href="{{config('app.expert_url')}}/expert/login">{{config('app.expert_url')}}/expert/login</a>
</p>

<p>If you have questions regarding the Dashboard, then
    please send your query to <a
        href="mailto:helpdashboard@cgpeu.com">helpdashboard@cgpeu.com</a></p>

<p>Kind regards,<br/>EAP Team</p>
</body>
</html>
