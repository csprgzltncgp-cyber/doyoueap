<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Dear {{$affiliate_search->to->name}}!</p>

<p><strong>{{$affiliate_search->from->name}}</strong> has assigned a new affiliate search to you!</p>

<p>Identifier: <strong>{{$affiliate_search->id}}</strong></p>
<p>Due date: <strong>{{\Carbon\Carbon::parse($affiliate_search->deadline)->format('Y-m-d')}}</strong></p>

<p>To see the details, please click on the link below:</p>
<a href="{{route( $affiliate_search->to->type . '.affiliate_searches.show', ['affiliateSearch' => $affiliate_search])}}">{{route( $affiliate_search->to->type . '.affiliate_searches.show', ['affiliateSearch' => $affiliate_search])}}</a>

<p>Best regards,
    <br/>
    EAP Team
</p>
</body>
</html>
