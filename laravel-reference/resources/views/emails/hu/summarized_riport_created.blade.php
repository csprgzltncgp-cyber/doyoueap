<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Kedves {{$user->name}}!</p>

<p>A <strong>{{$quarter}}</strong> negyedévre vonatkozó összesített jelentés elkészült.</p>
<p>Letöltési link: </p> <a href="{{config('app.url') .'/summarized-riport-exports/' . $filename}}">{{config('app.url') .'/summarized-riport-exports/' . $filename}}</a>

</body>
</html>
