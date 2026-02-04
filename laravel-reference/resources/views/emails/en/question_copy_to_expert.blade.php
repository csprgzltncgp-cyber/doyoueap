<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <p>Dear {{$user->name}}!</p>

    <p>We have received your message. Our colleague will get in touch with you on {{$user->email}} shortly.</p>

    <p>The message sent by you:</p>

    <p>{{$question}}</p>

    <p>Best regards,<br/>
      EAP Team</p>
</body>
</html>
