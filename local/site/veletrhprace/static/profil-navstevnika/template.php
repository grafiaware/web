<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Model\Entity\LoginAggregateCredentialsInterface;;
use Model\Repository\EnrollRepo;
use Model\Repository\StatusSecurityRepo;

$personalData = [
    [
        'fotografie' => [
            'src' => 'images/moje-krasna-fotka.jpg',
            'alt' => 'Profilový obrázek',
            'width' => '',
            'height' => '',
        ],
        'titulPred' => '',
        'titulPO' => '',
        'jmeno' => 'Novák',
        'prijmeni' => 'Novák',
        'email' => 'novak@nereknu.cz',
        'telefon' => '+420 123 456 789',
        'pracPopis' => 'Momentálně bez práce',
        'nahraneSoubory' => [
            'zivotopis' => 'cesta k souboru',
        ],
    ]
];

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
    $enrollRepo = $container->get(EnrollRepo::class);
    $enrolls = $enrollRepo->findByLoginName($loginName);

    $headline = "Můj profil";
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