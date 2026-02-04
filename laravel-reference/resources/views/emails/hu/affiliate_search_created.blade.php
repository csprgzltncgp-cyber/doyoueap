<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Kedves {{$affiliate_search->to->name}}!</p>

<p><strong>{{$affiliate_search->from->name}}</strong> új szakértő keresési feladatot rendelt hozzád!</p>

<p>Azonosító: <strong>{{$affiliate_search->id}}</strong></p>
<p>Határidő: <strong>{{\Carbon\Carbon::parse($affiliate_search->deadline)->format('Y-m-d')}}</strong></p>

<p>A részletek megtekintéséhez kattints az alábbi linkre:</p>
<a href="{{route( $affiliate_search->to->type . '.affiliate_searches.show', ['affiliateSearch' => $affiliate_search])}}">{{route( $affiliate_search->to->type . '.affiliate_searches.show', ['affiliateSearch' => $affiliate_search])}}</a>

<p>Üdvözlettel,<br/>
    EAP Team</p>
</body>
</html>
