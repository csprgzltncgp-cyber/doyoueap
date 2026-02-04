<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <p>Kedves {{$user->name}}!</p>

    <p>Az EAP programot a mai napon felkereste {{$case->case_client_name->value}}.</p>

    <p>Az eset megtekintéséhez kattintson az alábbi linkre: <a href="{{config('app.url')}}/expert/login?lang=hu&ref=case&id={{$case->id}}">Link</a></p>

    <p>Kérem, vegye fel {{$case->case_client_name->value}}-al a kapcsolatot a mai napon.</p>

    <p>Amennyiben kérdése lenne az esettel kapcsolatban kérjük, ne erre az emailre válaszoljon, hanem írjon a következő email címre az operátornak: <a href="mailto:{{$case->country->email}}">{{$case->country->email}}</a></p>

    <p>Kérem, ne feledje, a szerződésünk szerint a kapcsolatfelvételnek 2 munkanapon belül meg kell történnie az ügyféllel, majd ezt követően 5 napon belül meg kell kezdeni a tanácsadást, melyet 3 hónapon belül le kell zárni.</p>

    <p>Amennyiben nem tudja vállalni {{$case->case_client_name->value}} tanácsadását, kérem kattintson a ’Nem vállalom az esetet’ gombra az Expert Dashboard oldalon az eset adatlapján, még a mai napon.</p>

    <p>Amennyiben a link nem nyitná meg az Expert Dashboardot, írjon nekünk a <a href="mailto:helpdashboard@cgpeu.com">helpdashboard@cgpeu.com</a> email címre.</p>

    <p>Üdvözlettel,<br/>{{$operator->name}}</p>
  </body>
</html>
