<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <h1>Dear {{ $username }},</h1>

    <p>Please fillout the following survey(s):</p>

    @foreach ($links as $link)
      <a href="{{$link}}">{{$link}}</a><br><br>
    @endforeach

    <p>Regards,<br/>
      EAP csapat</p>
  </body>
</html>
