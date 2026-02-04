<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<p>Kedves {{ $receiver }},</p>

<p>Ebben a levélben csatoljuk a {{Carbon\Carbon::now()->subMonthNoOverflow()->format('Y-m')}}. havi utilizációs adatok.</p>

<p>Üdvözlettel,<br/>
    EAP Team</p>
</body>
</html>
