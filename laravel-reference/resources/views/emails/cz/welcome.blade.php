<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<h1>Dear {{$user->name}}!</h1>

<p>Welcome among our partners! During the EAP programme, we will connect you with clients through the self-developed
    website of CGP Europe called Experts’ Dashboard. We will send you a notification of every case assigned to you to
    the email address you have provided: {{$user->email}}</p>

<p>To visit Experts’ Dashboard, follow this link:</p>
<a href="{{config('app.expert_url')}}/expert/login?lang=en">{{config('app.expert_url')}}</a>

<p>The login details of your personal page at Experts’ Dashboard are the following:</p>

<p style="margin-bottom:0px;">Username: {{$user->username}}</p>
<p style="margin-top:0px;">Password: {{$user->username}}9872346</p>

<p>Please use this username and password to log in to Experts’ Dashboard as soon as possible.
    You can
    confirm your account by logging in to the site for the first time. Please note that we can
    only connect
    you with clients in the future after you have confirmed your account.</p>

<p>Without that, there will be no cases waiting for you after logging in to Experts’ Dashboard.
    The site
    doesn’t store previous cases, those have to be documented, closed and invoiced as before.
    Your Dashboard
    will be gradually filled with content as you get assigned new cases.</p>

<p>You can change your password after logging in, under the ‘Password Setting’ option.</p>

<p>What are the advantages of Experts’ Dashboard?</p>

<p>You can closely track the current status of your cases, check which ones are ready to be
    invoiced, which
    ones are waiting for approval; you can quickly react to new cases, providing up to date
    information to
    operators.</p>

<p>What can you use the Experts’ Dashboard website for?</p>

<p>You can accept or decline new cases, record getting in touch with the client, register
    consultation
    appointments in the case profile, and conclude cases by uploading client satisfaction
    surveys.</p>

<p>Please check out and read the attached User Guide to Experts’ Dashboard in order to start
    using this
    platform as soon and as easily as possible.</p>

<p style="margin-bottom:0px">Should you have any questions about Experts’ Dashboard, please send
    us an email
    to:</p>

<a href="mailto:helpdashboard@cgpeu.com">helpdashboard@cgpeu.com</a>

<p>Thank you for your cooperation!</p>

<p>Best regards,
    <br/>
    EAP Team
</p>
</body>
</html>
