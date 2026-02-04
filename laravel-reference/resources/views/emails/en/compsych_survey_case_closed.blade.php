<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <h1>Dear {{ $username }},</h1>

    <p>Please answer the follow surveys, as ordered by Compsych</p>

    @foreach ($links as $link)
      <a href="{{$link}}">{{$link}}</a><br><br>
    @endforeach

    <p>Kind regards,<br/>
      EAP team</p>
  </body>
</html>
