<?php

namespace App\Console\Commands;

use App\Mail\ExpertInformatonEmail;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendExpertInformationEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-expert-information-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        set_time_limit(3600);

        $langs = [
            'hu' => [
                'info@felegyhazyanna.hu',
                'tidud@freemail.hu',
                'egercsaba89@gmail.com',
                'eitler.szilvia@gmail.com',
                'adorjani.balazs@gmail.com',
                'drkatonacintia@gmail.com',
                'tothnora91@gmail.com',
                'agnes@pszicho-mediator.hu',
                'timea.torok6000@gmail.com',
                'kanizsamed@gmail.com',
                'beke.emese@gmail.com',
                'orsi@szobeczki.hu',
                'hajninyeste@gmail.com',
                'halascsilla@gmail.com',
                'pszitan@gmail.com',
                'da.levente@gmail.com',
                'konferencia.nagyildiko@gmail.com',
                'agota.lakatosgerlach@gmail.com',
                'svab.ildiko@gmail.com',
                'szekeres.kristof@pszichologusbudapest.org',
                'fhpszichologus@gmail.com',
                'judit.heim1@gmail.com',
                'kitanics.szakerto@gmail.com',
                'cseke.otilia@gmail.com',
                'gabi.szeman0@gmail.com',
                'juciko@gmail.com',
                'eva.stefankovics@gmail.com',
                'tothagnes23@gmail.com',
                'becazo@gmail.com',
                'edina.farkasdi@gmail.com',
                'manwolfman999@gmail.com',
                'jozsafodor@gmail.com',
                'aprnhelpcare@gmail.com',
                'jozsef.kornel.torok@gmail.com',
                'info@fitfount.hu',
                'aliz771019@gmail.com',
                'buzhenriett@gmail.com',
                'zentai.norbert8@gmail.com',
                'bettina.lnt@gmail.com',
                'bremfit@gmail.com',
                'zsuzso84@gmail.com',
                'crossfit.gazsi@gmail.com',
                'gaborpetro@freemail.hu',
                'asztalos.adrienn82@gmail.com',
                'czuczai.gabriella@gmail.com',
                'petrinori@gmail.com',
                'anitadieta@gmail.com',
                'evelyn.asiama@gmail.com',
                'hojcskanoemi@gmail.com',
                'katalin.koroknay@gmail.com',
                'csordas.ildi@gmail.com',
                'akosugyved@gmail.com',
                'dr.bayer.monika@gmail.com',
                'ugyved@bodispal.hu',
                'info@meszaros-kincses.hu',
                'agnesdredes@gmail.com',
                'drmocsaizoltan@t-online.hu',
                'lgalos@gmail.com',
                'drsolymosi@koroskabel.hu',
                'info@drfeketegabriella.hu',
                'ugyved@drjuhaszzoltan.hu',
                'iroda@kollarandor.hu',
                'drfekete.zoltan@chello.hu',
                'evapat30@gmail.com',
                'iroda@vadasjanos.hu',
                '1.takacszsuzsa@gmail.com',
                'judit.kasa@gmail.com',
                'nemeth.judit@mtdtanacsado.hu',
                'aniko.kuzdy@gmail.com',
                'czegeny.e@gmail.com',
                'novomeszky@yahoo.co.uk',
                'cutlergymkaposvar@gmail.com',
                'eap.hungary@cgpeu.com',
                'eap.hungary@cgpeu.com',
                'krisztinawek@gmail.com',
                'iroda@drladik.hu',
                'jan.svehlik@gmail.com',
                'bzsadrienn@gmail.com',
                'kovacs.neszta@gmail.com',
                'krizistanacsadas@gmail.com',
                'juciko@gmail.com',
                'nora@hobornoraugyvediiroda.hu',
                'tcsilla94@gmail.com',
                'peri79ster@gmail.com',
                'dr.santha.agnes@gmail.com',
                'lelekkepzo@gmail.com',
                'info@kocsisdora.hu',
                'Borbala.burinda@cgpeu.com',
            ],
            'pl' => [
                'joanna.sztuka@cgpeu.com',
                'szabiello@st.swps.edu.pl',
                'kawruk@op.pl',
                'pomost@pomost.edu.pl',
                'solutionfocusedmind@gmail.com',
                'syrok@tlen.pl',
                'prosukces.pl@gmail.com',
                'iwona_kus@onet.eu',
                'gesp_pogoda@wp.pl',
                'agakaletakieres@gmail.com',
                'iwona.wolkowicz@gmail.com',
                'malwina.h@poczta.fm',
                'piotrsmurawa@gmail.com',
                'olajanczyk@hotmail.com',
                'gabinet@serceiumysl.pl',
                'magdalena.dlugolecka@dobrostan.org.pl',
                'rac.justyna@gmail.com',
                'awisnia25@gmail.com',
                'joanna.starzecka@interia.pl',
                'kaminskaaga@poczta.fm',
                'martapasternak@psychobalance.pl',
                'marekjablonski.lbn@gmail.com',
                'propsyche.lublin@gmail.com',
                'koszalin.psychoterapia@gmail.com',
                'oliviastupar@gmail.com',
                'tomasz.matracki@gmail.com',
                'jtokarz@sanity-gabinet.pl',
                'aadamkowska46@gmail.com',
                'martascislo@interia.pl',
                'kara.kwiatkowska@gmail.com',
                'ilonaadamczyk12@gmail.com',
                'rafal@praktykapsychologiczna.net',
                'bliwowska@wp.pl',
                'info@psychokompas.pl',
                'klaudia.pinkowska@gmail.com',
                'l.karasinski@outlook.com',
                'Magdalena.jaromi@gmail.com',
                'marta.heller_surowiec@mojdietetyk.pl',
                'patrycja.pawlikowska@mojdietetyk.pl',
                'ola@jeszfresh.pl',
                'gabinet@masaz-zywiec-pl',
                'michalgac5@gmail.com',
                'kontakt@mindyourbody.pl',
                'kontakt@adwokatmdp.pl',
                'adwokat@sulecka.pl',
                'weronika.szczepanik@ekancelariaszczepanik.pl',
                'j.birecka@birecka.com',
                'kancelaria@adwokatbienias.pl',
                'daniel.szczubelek@kancelaria-ds.pl',
                'biuro@koszalin-advokat.pl',
                'mmazurek@adwokatlezajsk.pl',
                'biuro@czternastek.pl',
                'eap.poland@cgpeu.com',
                'eap.poland@cgpeu.com',
                'itrybus@yahoo.com',
                'pracowniamomenty@gmail.com',
                'annakolbusz@tlen.pl',
                'sulimirs@gmail.com',
                'yspaden@gmail.com',
                'zygmunt.tylicki@tpa-legal.pl',
                'b_nowak@poczta.onet.pl',
                'jagodajosiak@hotmail.com',
                'zuziakrz@op.pl',
            ],
            'cz' => [
                'martina.prich@gmail.com',
                'jirinamelzer@gmail.com',
                'parizkova@parizkova.eu',
                'roes.willi@gmail.com',
                'roes.daniela@gmail.com',
                'psycholog-komarkova@seznam.cz',
                'dpatikova@seznam.cz',
                'monika@imaginesolution.cz',
                'brzkovska@gmail.com',
                'kadlecek@kadlecek.com',
                'eap.czech@cgpeu.com',
                'eap.czech@cgpeu.com',
                'psycholog.pp@gmail.com',
                'sylvie.navarova@smrov.cz',
                'info@koubikovalegal.eu',
                'info@psycholog-metlicka.cz',
                'martina.fialova@akmf.cz',
                'peterzach42@gmail.com',
                'centrum@drstanek.cz',
                'vvymetal@gmail.com',
                'zdenka@centrumzmen.cz',
                'marketa.cer@gmail.com',
                'ales@neusar.cz',
            ],
            'sk' => [
                'husmed@gmail.com',
                'viktor@kosicecity.eu',
                'eap@enefconsulting.sk',
                'ivanvalkovic49@gmail.com',
                'jaroslava.kopcakova@gmail.com',
                'samuelschuerer@gmail.com',
                'csehkonyveles@gmail.com',
                'martincsaszar@gmail.com',
                'martin.provaznik@bpv-bp.com',
                'rohacek@profigure.sk',
                'eap.slovak@cgpeu.com',
                'eap.slovak@cgpeu.com',
            ],
            'ch' => [ // fr, de, it
                'mclaudius.phd@gmail.com',
                'ddanis@bluewin.ch',
                'antonella.bertoli@psychologie.ch',
                'dr.christine.reymond@gmail.com',
                'rebeccaseger@bluewin.ch',
                'olivier_guex@hotmail.com',
                'solange.bote@netplus.ch',
                'stefaniepfister@me.com',
                'ela.amarie@gmail.com',
                'info@marinakoch.ch',
                'vita-activa@mail.ch',
                'kaj.noschis@comportements.ch',
                's.pochon@psychologie.ch',
                'info@bienetreautravail.ch',
                'info@claudiahehli.ch',
                'eap@sutmos.com',
                'vl@pst-legalconsulting.ch',
                'eap-account-management@stimulus-conseil.com',
                'eap.switzerland@cgpeu.com',
                'eap.switzerland@cgpeu.com',
                'info@stephanscherrer.ch',
                'contact@innerways.at',
                'catalina.woldarsky@fsp-hin.ch',
                'sylvi@beyou-lye.ch',
                'info@zbinden-coaching.ch',
            ],
            'ro' => [
                'dorinaherczeg@hotmail.com',
                'arina.dogaru@gmail.com',
                'ilon.gorog@gmail.com',
                'carmenfabian_psi@yahoo.com',
                'nicoleta.capraru@gmail.com',
                'sanovitbac@yahoo.com',
                'ctinag22@gmail.com',
                'cocan.maria.anisia@gmail.com',
                'roxana.belean@yahoo.com',
                'stefan.luiza.sl@gmail.com',
                'dinuelenaandreea@gmail.com',
                'barbmakkai@gmail.com',
                'loredana_pod@yahoo.com',
                'office@themind.ro',
                'cristina.danea@psihart.ro',
                'alinaatalay@yahoo.com',
                'romlegaloffice@gmail.com',
                'av.andreeamihai@gmail.com',
                'eap.romania@cgpeu.com',
                'eap.romania@cgpeu.com',
                'iozefina.berbece@yahoo.com',
                'terapie.cluj@gmail.com',
                'lesutan@gmail.com',
                'psiholabcluj@gmail.com',
                'psy_dragan@yahoo.com',
                'contact@psihologirinamacovei.ro',
                'bogdana.bursuc@mindinstitute.ro',
            ],
            'sr' => [
                'pedjakuzm@yahoo.com',
                'vbajkovic@gmail.com',
                'ljubovanja@yahoo.com',
                'eap.serbia@cgpeu.com',
                'eap.serbia@cgpeu.com',
                'simic.ljubo@yahoo.com',
                'keelyfraser@gmail.com',
                'katarinadj@yahoo.com',
                'office@law-firm.rs',
                'nakena2@gmail.com',
            ],
            'lt' => [
                'info@psichologas.eu',
                's.jasiukaite@gmail.com',
                'vytaute.ladigaite@lawboutique.lt',
            ],
            'bg' => [
                'diana_ginova@abv.bg',
                'kidakova@gmail.com',
                'eva.ancheva@gmail.com',
                'elena@gestalt-bulgaria.org',
                'petio.hristov@gmail.com',
                'abakoev@gmail.com',
                'vesselinova@ataconsult.com',
                'eap.bulgaria@cgpeu.com',
                'eap.bulgaria@cgpeu.com',
            ],
            'de' => [
                'aykut.elseven@se-legal.de',
                'kerri.cummings@mind-bar.de',
                'info@psychotherapie-blagec.de',
            ],
            'al' => ['artan@bozolaw.al', 'r.pero@arsfirm.al'],
            'ko' => [ // sr, al
                'imrizabeli@gmail.com',
                'afrim.salihu@yahoo.com',
                'fitim.u@gmail.com',
            ],
        ];

        foreach ($langs as $lang => $mails) {
            foreach ($mails as $mail) {
                try {
                    $this->setLanguage($lang);
                    Mail::to($mail)->send(new ExpertInformatonEmail);
                } catch (Exception) {
                    Log::info('Failed to send email to: '.$mail);
                }
            }
        }
    }

    private function setLanguage(string $code): void
    {
        $lang = $code ?: 'en';

        app()->setLocale($lang);

        session(['locale' => $lang]);
        session()->save();
    }
}
