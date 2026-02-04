<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <p>Drogi {{$user->name}}!</p>

    <p>Program EAP odwiedził dziś {{$case->case_client_name->value}}.</p>

    <p>Możesz obejrzeć wydarzenie, klikając następujący link: <a href="{{config('app.url')}}/expert/login?lang=pl&ref=case&id={{$case->id}}">Link</a></p>

    <p>Skontaktuj się z {{$case->case_client_name->value}} już dziś.</p>

    <p>Jeśli masz jakieś pytania dotyczące sprawy, nie odpowiadaj na tego e-maila, ale napisz na adres e-mail do agenta: <a href="mailto:{{$case->country->email}}">{{$case->country->email}}</a></p>

    <p>Należy pamiętać, że zgodnie z naszą umową należy się skontaktować z klientem w ciągu 2 dni roboczych, a doradztwo należy rozpocząć w ciągu najpóźniej  5 dni i zakończyć w ciągu 3 miesięcy.</p>

    <p>Jeśli nie możesz udzielić porady {{$case->case_client_name->value}}, kliknij „Odrzuć sprawę” w profilu sprawy na stronie Pulpitu eksperckiego nie później niż dziś.</p>

    <p>W przypadku problemów z otwarciem linku do sprawy wyślij wiadomość e-mail na adres <a href="mailto:helpdashboard@cgpeu.com">helpdashboard@cgpeu.com</a></p>

    <p>Z poważaniem,<br/>{{$operator->name}}</p>
  </body>
</html>
