<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Events\Model\Repository\EnrollRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Events\Model\Repository\VisitorProfileRepo;

use Pes\Text\Html;
use Pes\Text\Text;

$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateFullInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();

if (isset($loginAggregate)) {
    $loginName = $loginAggregate->getLoginName();
    $role = $loginAggregate->getCredentials()->getRole() ?? '';
}

// poue pro default roli 'visitor'
if (isset($role) AND $role==(ConfigurationCache::loginLogoutController()['roleVisitor'])) {

    $visitorDataRepo = $container->get(VisitorProfileRepo::class);
    $visitorData = $visitorDataRepo->get($loginName);

    $enrollRepo = $container->get(EnrollRepo::class);
    $enrolls = $enrollRepo->findByLoginName($loginName);

    $headline = "Můj profil";
    $perex = "Vítejte $loginName ";
    $zprava =
        [
            [
                'idZpravy' => '',
                'nazev' => 'Stále zde naleznete pracovní místa a odkazy na přednášky',
                'text' => "I když online veletrh s živou účastí skončil, stále zde naleznete přístupy k záznamům přednášek a prezentací, které chcete zhlédnout.

            V harmonogramu najdete akce, ke kterým jste se přihlásili.

            Stále si zde můžete na stránce přednášek a na stránkách zaměstnavatelů prohlédnout všechna dostupná videa na YouTube."
            ],[
                'idZpravy' => '',
                'nazev' => '',
                'text' => "Stále můžete oslovit zaměstnavatele. Nahrajte svůj životopis a motivační dopis a u vybraných firem rovnou vložte jako odpověď zájemce o pozici."
            ],
            [
                'idZpravy' => '',
                'nazev' => 'Anketa a slosování',
                'text' => 'Vyplňte <a href="web/v1/page/item/607400995acdd" target="_blank">ANKETU NÁVŠTĚVNÍKA</a> a zařaďte se do slosování o ceny, které proběhne 28. dubna!'
            ]
        ];
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
            <content>
                <?= $this->insert(ConfigurationCache::componentController()['templates']."zprava"."/template.php", $zprava) ?>
            </content>
            <content>
                <?php include "content/profil.php" ?> <!-- Tiny pro .working-data -->
            </content>
        </section>
    </article>

    <?php
} else {
    $headline = "Profil návštěvníka";
    $perex = 'I když online veletrh s živou účastí skončil, stále zde návštěvníci naleznou odkazy k záznamům přednášek a prezentací, které chtějí zhlédnout.';
    $zprava =
        [
            [
                'idZpravy' => '',
                'nazev' => 'Stále zde naleznete pracovní místa a odkazy na přednášky',
                'text' =>
                "Přihlášení návštěvníka bude fungovat po celý duben. Můžete se přihlásit, nahrát svůj životopis a motivační dopis a u vybraných firem je rovnou vložit jako odpověď zájemce o pozici."
            ],[

                'idZpravy' => '',
                'nazev' => '',
                'text' => "I po ukončení veletrhu se můžete nově registrovat a získat výhody registrovaného návštěvníka. I nově registrovaní mohou nahrát svůj životopis a motivační dopis a u vybraných firem je rovnou vložit jako odpověď zájemce o pozici."
            ]
        ];
    ?>
    <article class="paper">
        <section>
            <headline>
                <?php include "headline.php" ?>
            </headline>
            <perex>
                <?php include "perex.php" ?>
            </perex>
            <content>
                <?= $this->insert(ConfigurationCache::componentController()['templates']."zprava"."/template.php", $zprava) ?>
            </content>
        </section>
    </article>
    <?php
}
?>