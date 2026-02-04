<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
    <p>Kedves {{$reciever->name}}!</p>
    <p><strong>{{$sender->name}}</strong> új üzentet küldött az alábbi szakértő keresési feladattal kapcsolatban!</p>

    <p>Azonosító: <strong>{{$affiliateSearch->id}}</strong></p>
    <p>Határidő: <strong>{{\Carbon\Carbon::parse($affiliateSearch->deadline)->format('Y-m-d')}}</strong></p>

    <p>A részletek megtekintéséhez kattints az alábbi linkre:</p>

    @if($affiliateSearch->from_id == $reciever->id)
        <a href="{{route( $affiliateSearch->to->type . '.affiliate_searches.edit', ['affiliateSearch' => $affiliateSearch])}}">{{route( $affiliateSearch->to->type . '.affiliate_searches.edit', ['affiliateSearch' => $affiliateSearch])}}</a>
    @else
        <a href="{{route( $affiliateSearch->to->type . '.affiliate_searches.show', ['affiliateSearch' => $affiliateSearch])}}">{{route( $affiliateSearch->to->type . '.affiliate_searches.show', ['affiliateSearch' => $affiliateSearch])}}</a>
    @endif

    <p>Üdvözlettel,
        <br/>
        EAP Team
    </p>
</body>
</html>
