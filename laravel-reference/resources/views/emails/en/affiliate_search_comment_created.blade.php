<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Dear {{$reciever->name}}!</p>
<p><strong>{{$sender->name}}</strong> has sent a new message about the following affiliate search task!</p>

<p>Identifier: <strong>{{$affiliateSearch->id}}</strong></p>
<p>Due date: <strong>{{\Carbon\Carbon::parse($affiliateSearch->deadline)->format('Y-m-d')}}</strong></p>

<p>To see the details, please click on the link below:</p>

@if($affiliateSearch->from_id == $reciever->id)
<a href="{{route( $affiliateSearch->to->type . '.affiliate_searches.edit', ['affiliateSearch' => $affiliateSearch])}}">{{route( $affiliateSearch->to->type . '.affiliate_searches.edit', ['affiliateSearch' => $affiliateSearch])}}</a>
@else
    <a href="{{route( $affiliateSearch->to->type . '.affiliate_searches.show', ['affiliateSearch' => $affiliateSearch])}}">{{route( $affiliateSearch->to->type . '.affiliate_searches.show', ['affiliateSearch' => $affiliateSearch])}}</a>
@endif

<p>Best regards,
    <br/>
    EAP Team
</p>
</body>
</html>
