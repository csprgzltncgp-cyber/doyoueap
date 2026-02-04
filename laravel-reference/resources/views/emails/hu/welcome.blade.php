<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <h1>Kedves {{$user->name}}!</h1>

    <p>Köszöntjük a CGP Europe partnerei között! Az EAP program során az Ön számára klienseket fogunk kiközvetíteni a CGP Europe saját fejlesztésű weboldalán keresztül, melyet Experts’ Dashboardnak hívunk. Mindegy egyes kiközvetített esetről értesítést küldünk az Ön által korábban megadott e-mail címre: {{$user->email}}</p>

    <p>Az Experts’ Dashboardot az alábbi webcímen találja:</p>
    <a href="https://expertdashboard.chestnutce.com/expert/login?lang=hu">https://expertdashboard.chestnutce.com<a>

    <p>Az Ön belépési adatai, melyekkel elérheti az Experts’ Dashboardon a saját felületét, az alábbiak:</p>

    <p style="margin-bottom:0px;">Felhasználóneve: {{$user->username}}</p>
    <p style="margin-top:0px;">Jelszava: {{$user->username}}9872346</p>

    <p>Kérjük, mielőbb lépjen be az Expert Dashboard weboldalra felhasználónevével és jelszavával. Első belépése egyben regisztrációja aktiválását is jelenti. Kérjük, vegye figyelembe, hogy regisztrációja aktiválása nélkül sajnos nem tudunk eseteket kiközvetíteni az Ön számára a továbbiakban!</p>

    <p>Az Experts’ Dashboardon belépést követően nem fog találni kiközvetített eseteket. Korábban kiközvetített eseteket nem tartalmaz az oldal, azokat a korábbiak szerint szükséges dokumentálni, lezárni és számlázni. Tartalommal majd csak ezt követően telik meg a Dashboard, ahogy új eseteket közvetítünk ki az Ön számára.</p>

    <p>Ha szeretné megváltoztatni a jelszavát, az első belépést követően a ‘Jelszóbeállítás’ menüpontban megteheti.</p>

    <p>Milyen előnyökkel jár az Experts’ Dashboard használata?</p>

    <p>A weboldalon pontosan nyomon követheti esetei aktuális státuszát, láthatja melyik eset számlázható, melyik vár elfogadásra, könnyen és gyorsan reagálhat egy-egy eset kiközvetítésére, pontos információval szolgálva ezáltal az operátorok számára.</p>

    <p>Mit lehet csinálni az Expert Dashboard weboldalon?</p>

    <p>Ezen a weboldalon el lehet fogadni vagy épp el lehet utasítani az eset vállalását, lehet rögzíteni a kapcsolatfelvétel megtörténtét a klienssel, lehet konzultációs időpontokat rögzíteni az eset adatlapján és kliens elégedettségi kérdőív vagy kliens elégedettségi pontszámok megadásával le is lehet zárni az eseteket.</p>

    <p>Csatolunk egy rövid Használati Útmutatót az Expert Dashboardhoz, kérjük, lapozza át, olvassa el, hogy minél egyszerűbben és gyorsabban tudja használni a felületet.</p>

    <p style="margin-bottom:0px">Kérdéseit felteheti az Experts’ Dashboarddal kapcsolatban az alábbi e-mail címen:</p>
    <a href="mailto:helpdashboard@cgpeu.com">helpdashboard@cgpeu.com</a>

    <p>Együttműködését ezúton is köszönjük!</p>

    <p>Üdvözlettel,<br/>
      EAP Team</p>
  </body>
</html>
