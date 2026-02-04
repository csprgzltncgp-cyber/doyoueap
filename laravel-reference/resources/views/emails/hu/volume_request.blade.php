<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <p>@if(empty($salutation)) Kedves Partnerünk @else {{$salutation}} @endif!</p>

        <p>Kérjük, a {{$month}}. hónap vonatkozó munkavállalói létszám adatot küldje el részünkre az alábbi linkre kattintva.</p>

        <p>
            Amenyiben az adat nem kerül elküldésre, úgy az elöző hónap adata kerül felhasználásra a számlázás sorrán.
        </p>

        <p>
            <a href="{{$signed_link}}">{{$signed_link}}</a>
        </p>

        <p>Kérjük, erre az emailre ne válaszoljon!</p>

        <p>Üdvözlettel,<br>{{$sender}}</p>
    </body>
</html>
