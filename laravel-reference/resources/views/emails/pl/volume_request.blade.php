<!DOCTYPE html>
<html lang="pl" dir="ltr">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <p>@if(empty($salutation)) Szanowny Partnerze @else {{$salutation}} @endif!</p>

        <p>Proszę przesłać nam dane dotyczące liczby pracowników na {{$month}}. miesiąc, klikając w poniższy link.</p>

        <p>
            Jeśli dane nie zostaną przesłane, do celów fakturowania zostaną użyte dane z poprzedniego miesiąca.
        </p>

        <p>
            <a href="{{$signed_link}}">{{$signed_link}}</a>
        </p>

        <p>Proszę nie odpowiadać na ten email!</p>

        <p>Z poważaniem,<br>{{$sender}}</p>
    </body>
</html>