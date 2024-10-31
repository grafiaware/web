<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Events\Model\Repository\EnrollRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Events\Model\Repository\VisitorProfileRepo;
use Events\Model\Repository\VisitorProfileRepoInterface;
use Events\Model\Entity\VisitorProfileInterface;

use Events\Model\Repository\DocumentRepo;
use Events\Model\Repository\DocumentRepoInterface;
use Events\Model\Entity\DocumentInterface;

use Pes\Text\Html;
use Pes\Text\Text;

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;

 //------------------------------------------------------------------
    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);
    $role = $statusViewModel->getUserRole();
    $loginName = $statusViewModel->getUserLoginName();
//------------ $role $loginName prihlaseneho ---------------------------

//----------- alternativni ziskani udaju ------------------
///** @var StatusSecurityRepo $statusSecurityRepo */
//$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
///** @var StatusSecurityInterface $statusSecurity */
//$statusSecurity = $statusSecurityRepo->get();
///** @var LoginAggregateFullInterface $loginAggregate */
//$loginAggregate = $statusSecurity->getLoginAggregate();
//if (isset($loginAggregate)) {
//    $loginName = $loginAggregate->getLoginName();
//    $role = $loginAggregate->getCredentials()->getRoleFk() ?? '';    
//------------------------------------------------------------------

$isVisitor = isset($role) AND $role==(ConfigurationCache::loginLogoutController()['roleVisitor']);

// pouze pro  roli 'visitor' , tj. prihlaseneho navstevnika
if ($isVisitor) {
    /** @var VisitorProfileRepoInterface $visitorProfileRepo */
    $visitorProfileRepo = $container->get(VisitorProfileRepo::class);
    $visitorProfile = $visitorProfileRepo->get($loginName);   

    /** @var VisitorProfileInterface $visitorProfile */
    if (isset($visitorProfile)) {
        $documentCvId = $visitorProfile->getCvDocument();
        $documentLettterId = $visitorProfile->getLetterDocument();        
    }
    /** @var DocumentRepoInterface $documentRepo */
    $documentRepo = $container->get(DocumentRepo::class);
    /** @var DocumentInterface $visitorDocumentCv */
    if (isset($documentCvId)) {
        $visitorDocumentCv = $documentRepo->get($documentCvId);        
    }
    /** @var DocumentInterface $visitorDocumentLetter */
    if (isset($documentLettterId)) {
        $visitorDocumentLetter = $documentRepo->get($documentLettterId);         
    }
    
    
                    //--------------------------------------------------?????????
                    $enrollRepo = $container->get(EnrollRepo::class);
                    $enrolls = $enrollRepo->findByLoginName($loginName);
                    //--------------------------------------------------?????????
    
    

    $headline = "Můj profil";
    $perex = "Vítejte návštěvníku $loginName! " ;
    $zpravy =
        [   [   'idZpravy' => '',
                'nazev' => 'Stále zde naleznete pracovní místa a odkazy na přednášky',
                'text' => "I když online veletrh s živou účastí skončil, stále zde naleznete přístupy k záznamům přednášek a prezentací, které chcete zhlédnout.                    
            V harmonogramu najdete akce, ke kterým jste se přihlásili.
            Stále si zde můžete na stránce přednášek a na stránkách zaměstnavatelů prohlédnout všechna dostupná videa na YouTube."
            ],
            [   'idZpravy' => '',
                'nazev' => '',
                'text' => "Stále můžete oslovit zaměstnavatele. Nahrajte svůj životopis a motivační dopis a u vybraných firem rovnou vložte jako odpověď zájemce o pozici."
            ],
            [   'idZpravy' => '',
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
                <?= $this->insert(ConfigurationCache::commonTemplates()['templates']."layout/info/zpravy.php", $zpravy) ?>
            </content>
            <content>
                <?php include "content/profil.php" ?>       <!-- Tiny pro .edit-userinput -->
            </content>
        </section>
    </article>

    <?php
} // jen pro visitor roli-konec






else {
    $headline = "Profil návštěvníka";
    $perex = 'I když online veletrh s živou účastí skončil, stále zde návštěvníci naleznou odkazy k záznamům přednášek a prezentací, které chtějí zhlédnout.';
    $zpravy =
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
                <?= $this->insert(ConfigurationCache::commonTemplates()['templates']."layout/info/zpravy.php", $zpravy) ?>
            </content>
        </section>
    </article>
    <?php
}
?>