<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Hello {{$user->name}}!</p>

<p>Otrzymujesz tę wiadomość e-mail, ponieważ otrzymaliśmy prośbę o zresetowanie hasła do Twojego konta.</p>

<p>Twoje nowe hasło to: {{$password}}</p>

<p>Teraz możesz zalogować się na swoje konto za pomocą nowego hasła pod tym linkiem: <a
        href="https://expertdashboard.chestnutce.com/expert/login">https://expertdashboard.chestnutce.com/expert/login</a>
</p>

<p>Jeśli masz pytania dotyczące Tablicy eksperta wyślij zapytanie do <a
        href="mailto:helpdashboard@cgpeu.com">helpdashboard@cgpeu.com</a> email címre.</p>

<p>Z poważaniem,<br/>Zespół EAP</p>
</body>
</html>
