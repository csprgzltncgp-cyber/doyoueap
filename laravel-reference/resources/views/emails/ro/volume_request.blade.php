<!DOCTYPE html>
<html lang="ro" dir="ltr">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <p>@if(empty($salutation)) Stimate Partener @else {{$salutation}} @endif!</p>

        <p>Vă rugăm să ne trimiteți datele despre numărul de angajați pentru luna {{$month}} făcând clic pe linkul de mai jos.</p>

        <p>
            Dacă datele nu sunt trimise, vor fi utilizate datele din luna anterioară pentru facturare.
        </p>

        <p>
            <a href="{{$signed_link}}">{{$signed_link}}</a>
        </p>

        <p>Vă rugăm să nu răspundeți la acest email!</p>

        <p>Cu stimă,<br>{{$sender}}</p>
    </body>
</html>
