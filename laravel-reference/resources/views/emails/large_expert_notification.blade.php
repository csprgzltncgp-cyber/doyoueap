<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Kedves Tompa Anita!</p>

<p>A alábbi adatokkal  <strong>10 000</strong> eurót meghaladó számla került kiállításra.</p>

<p>Szakértő neve: <strong>{{$expert_name}}</strong></p>
<p>Érték: <strong>{{number_format((float) str_replace(' ', '', $amount), 0, ',', ' ')}} EUR</strong></p>
<p>Dátum: <strong>{{$date}}</strong></p>
<p>Számla sorszáma: <strong>{{$invoice_number}}</strong></p>

<p>Üdvözlettel,<br />
    CGP Europe
<p>
</body>
</html>
