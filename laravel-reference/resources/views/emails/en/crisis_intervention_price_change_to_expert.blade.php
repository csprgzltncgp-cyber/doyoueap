<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Dear {{$user->name}}!</p>

<p>We're writing to let you know that we're sending you a new offer to a crisis intervention with the ID
    of {{$case->activity_id}}.</p>

<p>You can find the new offer on the Experts ’Dashbaord website in the Crisis interventions menu in the crisis intervention data sheet with a
    yellow highlight.</p>

<p>The Experts’ Dashbaord website is available at this link:<a href="{{config('app.expert_url')}}">{{config('app.expert_url')}}</a>
</p>

<p>
    Please visit the Dashbaord page and, if possible, indicate on the crisis intervention data sheet today whether you accept the
    proposed award. To send a new offer, click on the yellow field and enter your preferred price.</p>

<p>If you have any questions about the crisis intervention, please do not reply to this email, but write to <a
        href="mailto:maria.szabo@cgpeu.com">maria.szabo@cgpeu.com</a></p>

<p>If you have questions regarding the Dashboard or experience any difficulties in opening the Crisis interventions datasheet, then
    please send your query to <a
        href="mailto:helpdashboard@cgpeu.com">helpdashboard@cgpeu.com</a>.</p>

<p>Kind regards,<br/>EAP Team</p>
</body>
</html>
