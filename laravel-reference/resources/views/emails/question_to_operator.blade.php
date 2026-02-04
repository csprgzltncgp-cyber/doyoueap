<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
  <p>Kedves {{$operator->name}}!</p>

  <p>E-mail üzenet érkezett számodra a {{$case->id}} azonosítóval rendelkező esettel kapcsolatban következő e-mail cím postafiókjába a Roundcube-on.</p>

  <p>Az e-mail tárgya: {{\Carbon\Carbon::parse($case->created_at)->format('Y-m-d') . ' - ' . ($case->company ? $case->company->name : '' ) . ' - '. $case->id . ' - ' . $operator->name }}</p>

  <p>Feladója: {{$user->email}}</p>

  <p>Kérlek, mielőbb lépj be {{$country->email}} e-mail cím postafiókjába a Roundcue-on és küldj onnan válaszüzenetet a szakértőnek.</p>

  <p>Üdvözlettel,</br>
  CGP Europe</p>
</body>
</html>
