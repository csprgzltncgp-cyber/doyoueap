<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <p>Dear {{$user->name}}!</p>

    <p>The EAP program was visited by {{$case->case_client_name->value}} today.</p>

    <p>You may view the event by clicking on the following link: <a href="{{config('app.url')}}/expert/login?ref=case&lang=en&id={{$case->id}}">Link</a></p>

    <p>Please contact {{$case->case_client_name->value}} today.</p>

    <p>If you have any questions regarding this case, please do not reply to this email, but send it to the following email address to the agent: <a href="mailto:{{$case->country->email}}">{{$case->country->email}}</a></p>

    <p>Please remember that the Client must be contacted within 2 business days per our agreement, and counselling must begin within 5 days subsequently and must be completed within 3 months.</p>

    <p>Should you be unable to provide counselling to {{$case->case_client_name->value}} please click on 'Decline case' in the case profile on the Experts’ Dashboard page no later than today.</p>

    <p>Should you have any issues opening the link to the case, please send an email to <a href="mailto:helpdashboard@cgpeu.com">helpdashboard@cgpeu.com</a></p>

    <p>Kind regards,<br/>{{$operator->name}}</p>
  </body>
</html>
