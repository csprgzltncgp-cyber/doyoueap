<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Dear {{$user->name}}!</p>

<p>Within the confines of the EAP program we would like to invite you to hold an Health Day.</p>

<p>You will find the assigned activity datasheet on the Experts’ Dashboard under the Other activity menu.</p>

<p>Please find the Experts’ Dashboard on the following link: <a href="{{config('app.expert_url')}}">{{config('app.expert_url')}}</a>
</p>

<p>We would kindly ask you to please visit this webpage preferably today and on the datasheet indicate whether you will
    be able to accept the assignment.</p>

<p>If you have any questions regarding the health day, then please write to <a href="mailto:maria.szabo@cgpeu.com">maria.szabo@cgpeu.com</a>.
    Please don’t send your questions as a reply to this email.</p>

<p>If you won’t be able to hold the health day, then we would ask you to please press the decline button on the data sheet
    within Experts’ Dashboard.</p>


<p>If you have questions regarding the Dashboard or experience any difficulties in opening the Other activity datasheet, then
    please send your query to <a
        href="mailto:helpdashboard@cgpeu.com">helpdashboard@cgpeu.com</a>.</p>

<p>Kind regards,<br/>EAP Team</p>

</body>
</html>
