<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Model\Repository\StatusSecurityRepo;
use Model\Entity\LoginAggregateCredentialsInterface;;
use Model\Entity\CredentialsInterface;

use Model\Repository\EnrollRepo;



$company = [
  1 => ["name" => "Wienerberger s.r.o.", "eventInstitutionName"=>"'Wienerberger'"],
  2 => ["name" =>"Daikin Industries Czech Republic s.r.o.", "eventInstitutionName"=>"'Daikin'"],
  3 => ["name" => "AKKA Czech Republic s.r.o.", "eventInstitutionName"=>"AKKA Czech Republic"],
  4 => ["name" => "MD ELEKTRONIK spol. s r.o.", "eventInstitutionName"=>"MD Elektronik"],
  5 => ["name" => "", "eventInstitutionName"=>"Konplan"],
  5 => ["name" => "", "eventInstitutionName"=>"Valeo Autoklimatizace"],
  6 => ["name" => "", "eventInstitutionName"=>"Stoelzle Union s.r.o."],
  7 => ["name" => "Grafia s.r.o.", "eventInstitutionName"=>"Grafia"],

];

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
    'User5' => ['regname' => "User5", 'regmail' => "svoboda@grafia.cz", 'regcompany' => "TEST", 'idCompany'=>3],
// maji nastevenu roli "presenter" v credentials
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
        <!--<content>-->
            <?php include "content/profil.php" ?> <!-- Tiny pro .working-data -->
        <!--</content>-->
    </section>
</article>

<?php
} else {

    $headline = "Profil vystavovatele";
    $perex = "Přihlášený vystavovatel zde uvidí své aktivity.";



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

