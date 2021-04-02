<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\Configuration;

use Model\Entity\LoginAggregateCredentialsInterface;;
use Model\Repository\EnrollRepo;
use Model\Repository\StatusSecurityRepo;
use Model\Repository\VisitorDataRepo;

use Pes\Text\Html;
use Pes\Text\Text;


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
    $role = $loginAggregate->getCredentials()->getRole() ?? '';
    $personalData['userHash'] = $loginAggregate->getLoginNameHash();
    $visitorDataRepo = $container->get(VisitorDataRepo::class);
    $visitorData = $visitorDataRepo->get($loginName);

    $enrollRepo = $container->get(EnrollRepo::class);
    $enrolls = $enrollRepo->findByLoginName($loginName);

    $headline = "Můj profil";
    $perex = "Vítejte {$loginAggregate->getLoginName()}



            ";

    $zprava =
        [
            [
                'idZpravy' => '',
                'nazev' => 'Stále zde naleznete pracovní místa, přednášky i poradny',
                'text' =>
                "I když online veletrh s živou účastí skončil, stále zde naleznete přístupy záznamům přednášek a prezentací, na které se přihlásili a které chcete shlédnout.

            V harmonogramu najdete akce, ke kterým jste se přihlásili.

            Stále si zde můžete na stránce přednášek a na stránkách zaměstnavatelů prohlédnout všechna dostupná videa na YouTube. 
                "
            ],[

                'idZpravy' => '',
                'nazev' => 'Stále se můžete registrovat a získat příležitost',
                'text' => "Nahrajte svůj životopis a motivační dopis a u vybraných firem rovnou vložte jako odpověď zájemce o pozici.
"
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
            <?= $this->insert(Configuration::componentControler()['templates']."zprava"."/template.php", $zprava) ?>
        </content>
        <content>
            <?php include "content/profil.php" ?> <!-- Tiny pro .working-data -->
        </content>
    </section>
</article>

<?php
} else {
    $headline = "Profil návštěvníka";
    $perex = 'I když online veletrh s živou účastí skončil stále zde návštěvníci naleznou přístupy záznamům, na které se přihlásili a které chtějí shlédnout.';
    $zprava =
        [
            [
                'idZpravy' => '',
                'nazev' => 'Stále zde naleznete pracovní místa, přednášky i poradny',
                'text' =>
                "I když online veletrh s živou účastí skončil, stále ještě máte možnost se přihlásit. Nahrajte svůj životopis a motivační dopis a u vybraných firem rovnou
                vložte jako odpověď zájemce o pozici.

                Prohlédněte si videa z přednášek a z prezentací zaměstnavatelů, na které jste se přihlásili a které chcete shlédnout.
                "
            ],[

                'idZpravy' => '',
                'nazev' => 'Stále se můžete registrovat a získat příležitost',
                'text' => "Můžete se také i nově zaregistrovat a získat výhody registrovaného návštěvníka. I nově registrovaní mohou nahrát svůj životopis a motivační dopis a u vybraných firem rovnou
                            vložit jako odpověď zájemce o pozici. "
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
            <?= $this->insert(Configuration::componentControler()['templates']."zprava"."/template.php", $zprava) ?>
        </content>
    </section>
</article>

<?php

}
?>