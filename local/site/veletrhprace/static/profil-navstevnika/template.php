<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Model\Entity\LoginAggregateCredentialsInterface;;
use Model\Repository\EnrollRepo;
use Model\Repository\StatusSecurityRepo;
use Model\Repository\VisitorDataRepo;



$igelitkaLetakAttributes = ['class' => 'letak-v-igelitce'];
$igelitka = [
    'letak' => [
        [
            'letakAttributes' => $igelitkaLetakAttributes +
            [
                'src' => 'images/letak-na-prednasku.jpg',
                'alt' => 'leták1',
            ],
            'downloadAttributes' => [
                'href' => 'download/letak-na-prednasku.pdf',
                'download' => 'leták 1',
            ]
        ],
        [
            'letakAttributes' => $igelitkaLetakAttributes +
            [
                'src' => 'images/moje-budoucnost-letakA5.jpg',
                'alt' => 'leták2',
            ],
            'downloadAttributes' => [
                'href' => 'download/moje-budoucnost-letakA5.pdf',
                'download' => 'leták 2',
            ]
        ]
    ],
];

$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateCredentialsInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();

if (isset($loginAggregate)) {
    $loginName = $loginAggregate->getLoginName();

    $personalData['userHash'] = $loginAggregate->getLoginNameHash();
    $visitorDataRepo = $container->get(VisitorDataRepo::class);
    $visitorData = $visitorDataRepo->get($loginName);

    $enrollRepo = $container->get(EnrollRepo::class);
    $enrolls = $enrollRepo->findByLoginName($loginName);

    $headline = "Můj profil";
    $perex = "Vítejte ".$loginAggregate->getLoginName().
            "

V harmonogramu najdete akce, ke kterým jste se přihlásili.

Před zahájením akce zde uvidíte barevný odkaz pro vstup na akci nebo odkaz pro zhlédnutí vybraného videa.
";
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
    $headline = "Profil návštěvníka";
    $perex = 'Zaregistrovaní návštěvníci zde po přihlášení naleznou přístupy k akcím, na které se přihlásili a které chtějí shlédnout.';

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
?>