<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Model\Repository\StatusSecurityRepo;
use Model\Entity\LoginAggregateCredentialsInterface;;
use Model\Entity\CredentialsInterface;

use Model\Repository\EnrollRepo;

        $readonly = 'readonly="1"';
        $disabled = 'disabled="1"';
//        $readonly = '';
//        $disabled = '';

$company = [
  1 => ["name" => "Wienerberger s.r.o.", "eventInstitutionName"=>"'Wienerberger'"],
  2 => ["name" =>"Daikin Industries Czech Republic s.r.o.", "eventInstitutionName"=>"'Daikin'"],
  3 => ["name" => "AKKA Czech Republic s.r.o.", "eventInstitutionName"=>"AKKA Czech Republic"],
  4 => ["name" => "MD ELEKTRONIK spol. s r.o.", "eventInstitutionName"=>"MD Elektronik"],
  5 => ["name" => "", "eventInstitutionName"=>"Konplan"],
  5 => ["name" => "", "eventInstitutionName"=>"Valeo Autoklimatizace"],
  6 => ["name" => "Stoelzle Union s.r.o.", "eventInstitutionName"=>"Stoelzle"],
  7 => ["name" => "Grafia s.r.o.", "eventInstitutionName"=>"Grafia"],

];

//                                <option value="Akka">AKKA Czech Republic s.r.o.</option>
//                                <option value="Daikin">Daikin Industries Czech Republic s.r.o.</option>
//                                <option value="DZK">Drůbežářský závod Klatovy a.s.</option>
//                                <option value="Grafia" selected>Grafia, s.r.o.</option>
//                                <option value="Kermi">Kermi, s.r.o.</option>
//                                <option value="Konplan">Konplan s.r.o.</option>
//                                <option value="MD">MD Elektronik s.r.o.</option>
//                                <option value="Possehl">Possehl Electronics Czech Republic s.r.o.</option>
//                                <option value="Stoelzle">STOELZLE UNION s.r.o.</option>
//                                <option value="UP">Úřad práce ČR a EURES</option>
//                                <option value="Valeo">Valeo Autoklimatizace k.s.</option>
//                                <option value="Wienerberger">Wienerberger s.r.o.</option>

$presenterArray =
[
    'Krejčová' => ['regname' => "Krejčová", 'regmail' => "barbora.krejcova@wienerberger.com", 'regcompany' => "Wienerberger s.r.o.", 'idCompany'=>1],

    'Tomáš Matoušek' => ['regname' => "Tomáš Matoušek", 'regmail' => "matousek.t@daikinczech.cz", 'regcompany' => "Daikin Industries Czech Republic s.r.o.", 'idCompany'=>2],

    'Elizabeth Franková' => ['regname' => "Elizabeth Franková", 'regmail' => "Elizabeth.frankova@akka.eu", 'regcompany' => "Akka Czech Republice s.r.o.", 'idCompany'=>3],
    'KaterinaJanku' => ['regname' => "KaterinaJanku", 'regmail' => "katerina.janku@akka.eu", 'regcompany' => "AKKA Czech Republic", 'idCompany'=>3],
    'Šárka Bilíková' => ['regname' => "Šárka Bilíková", 'regmail' => "sarka.bilikova@akka.eu", 'regcompany' => "AKKA Czech Republic s.r.o.", 'idCompany'=>3],

    'VERONIKA' => ['regname' => "VERONIKA", 'regmail' => "Veronika.Simbartlova@md-elektronik.cz", 'regcompany' => "MD ELEKTRONIK sro", 'idCompany'=>4],
    'Zdeňka Obertíková' => ['regname' => "Zdeňka Obertíková", 'regmail' => "Zdenka.Obertikova@md-elektronik.cz", 'regcompany' => "MD ELEKTRONIK s.r.o.", 'idCompany'=>4],
    'Kristýna Křížová' => ['regname' => "Kristýna Křížová", 'regmail' => "kristyna.krizova@md-elektronik.cz", 'regcompany' => "MD ELEKTRONIK spol. s r.o.", 'idCompany'=>4],

    'Michaela Šebová' => ['regname' => "Michaela Šebová", 'regmail' => "michaela.sebova@stoelzle.com", 'regcompany' => "Stoelzle Union s.r.o.", 'idCompany'=>6],  // nemá žádný event v EventListu

    'Vanda Štěrbová' => ['regname' => "Vanda Štěrbová", 'regmail' => "vanda.sterbova@akka.eu", 'regcompany' => "AKKA Czech Republic s.r.o.", 'idCompany'=>3],

    'Jana Brabcová' => ['regname' => "Jana Brabcová", 'regmail' => "brabcova@grafia.cz", 'regcompany' => "Grafia s.r.o.", 'idCompany'=>7],
    'Jana Brabcová Grafia OK' => ['regname' => "Jana Brabcová Grafia OK", 'regmail' => "brabcova@grafia.cz", 'regcompany' => "Grafia s.r.o.", 'idCompany'=>7],
    'presenter' => ['regname' => "presenter", 'regmail' => "svoboda@grafia.cz", 'regcompany' => "TEST", 'idCompany'=>3],
// maji nastavenu roli "presenter" v credentials
];


$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateCredentialsInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();


if (isset($loginAggregate)) {
    $credentials = $loginAggregate->getCredentials();
    $role = $credentials->getRole();
    $loginName = $loginAggregate->getLoginName();

    $presenterItem = array_key_exists($loginName, $presenterArray) ? $presenterArray[$loginName] : null;
    if (isset($presenterItem)) {
        $presenterItem = array_merge($presenterItem, $company[$presenterItem['idCompany']]);
    }


}

/** @var EnrollRepo $enrollRepo */
$enrollRepo = $container->get(EnrollRepo::class);
$enrolls = $enrollRepo->findAll();


$eventCountById = [];
foreach ($enrolls as $enroll) {
    if (array_key_exists($enroll->getEventid(), $eventCountById)) {
        $eventCountById[$enroll->getEventid()]++;
    } else {
        $eventCountById[$enroll->getEventid()] = 1;
    }
}




if(isset($role) AND ($role=='presenter' OR $role=='sup')) {
    $headline = "Profil vystavovatele";
    $perex = $loginAggregate->getLoginName();
?>
<article class="paper">
    <section>
        <headline>
            <?php include "headline.php" ?>
        </headline>
        <perex>
            <?php include "perex.php" ?>
        </perex>
    </section>
    <section>
        <div class="field margin">
            <label>Společnost</label>
            <select <?= $disabled ?>>
                <?php
                foreach ($company as $idCompany => $comp) {
                ?>
                <option value="<?= $comp['name']?>" <?= $idCompany==$presenterItem['idCompany'] ? "selected" : "" ?> > <?= $comp['name']?></option>
                <?php
                }
                ?>
            </select>
        </div>

        <!--<content>-->
            <?php include "content/profil.php" ?> <!-- Tiny pro .working-data -->
        <!--</content>-->
    </section>
</article>

<?php
} else {

    $headline = "Profil vystavovatele";
    $perex = "Profil vystavovatele je dostupný pouze přihlášenému vystavovateli.";



?>
<article class="paper">
    <section>
        <headline>
            <?php include "headline.php" ?>
        </headline>
        <perex>
            <?php include "perex.php" ?>
        </perex>
    </section>
</article>


<?php
}

