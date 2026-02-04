<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <p>@if(empty($salutation)) Dear Partner @else {{$salutation}} @endif!</p>

        <p>Please send us the employee headcount data for the {{$month}}. month by clicking on the link below.</p>

        <p>
            If the data is not sent, the previous month's data will be used for invoicing purposes.
        </p>

        <p>
            <a href="{{$signed_link}}">{{$signed_link}}</a>
        </p>

        <p>Please do not reply to this email!</p>

        <p>Best regards,<br>{{$sender}}</p>
    </body>
</html>