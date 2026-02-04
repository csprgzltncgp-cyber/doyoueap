<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <h1>Kedves {{ $username }},</h1>

    <p>Kérjük töltse ki az alábbi kérdőíveket:</p>

    @foreach ($links as $link)
      <a href="{{$link}}">{{$link}}</a><br><br>
    @endforeach

    <p>Üdvözlettel,<br/>
      EAP csapat</p>
  </body>
</html>
