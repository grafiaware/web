<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

use Site\ConfigurationCache;
use Status\Model\Repository\StatusSecurityRepo;
use Auth\Model\Entity\LoginAggregateFullInterface;

use Events\Middleware\Events\Controler\VisitorProfileControler;

use Events\Model\Repository\VisitorProfileRepo;
use Events\Model\Entity\VisitorProfileInterface;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Entity\VisitorJobRequestInterface;
use Events\Model\Repository\DocumentRepo;
use Events\Model\Entity\DocumentInterface;
use Events\Model\Repository\RepresentativeRepo;

use Events\Model\Arraymodel\Presenter;

/** @var PhpTemplateRendererInterface $this */

###### kontext #######
// jobs expand -> $nazev; $mistoVykonu; $vzdelani; $popisPozice; $pozadujeme; $nabizime; + přidané $nazev, $container
$positionName = $nazev;    // potrebuje se v osobni-udaje_2

$isRepresentative = false;
$isVisitor = false;
$isVisitorDataPost = false;



####
$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateFullInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();
####

/** @var Presenter $presenterModel */
$presenterModel = $container->get( Presenter::class );

/** @var VisitorJobRequestRepo $visitorJobRequestRepo */
$visitorJobRequestRepo = $container->get(VisitorJobRequestRepo::class);
/** @var DocumentRepo $documentRepo */
$documentRepo = $container->get(DocumentRepo::class);
/** @var RepresentativeRepo $representativeRepo */
$representativeRepo = $container->get(RepresentativeRepo::class );


if (isset($loginAggregate)) {
    $loginName = $loginAggregate->getLoginName();
    $role = $loginAggregate->getCredentials()->getRole() ?? '';
    //*--------------------------------
    $isVisitor = $role==ConfigurationCache::loginLogoutController()['roleVisitor'];   
    
    $isRepresentative = ( isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
                                       AND  $representativeRepo->get($loginName, $companyId) );

    if ($isVisitor) {
        /** @var VisitorProfileRepo $visitorProfileRepo */
        $visitorProfileRepo = $container->get(VisitorProfileRepo::class);
        /** @var VisitorProfileInterface $visitorProfileEntity */
        $visitorProfileEntity = $visitorProfileRepo->get($loginName);

        /** @var VisitorJobRequestInterface $visitorJobRequestEntity */
        $visitorJobRequestEntity = $visitorJobRequestRepo->get($loginName, $jobId) ; // tady ma byt id jobu                                

        // formulář
        // unikátní jména souborů pro upload
        $userHash = $loginAggregate->getLoginNameHash();
        $accept = implode(", ", ConfigurationCache::filesUploadController()['upload.events.acceptedextensions']);
        $uploadedCvFilename = VisitorProfileControler::UPLOADED_KEY_CV.$userHash;
        $uploadedLetterFilename = VisitorProfileControler::UPLOADED_KEY_LETTER.$userHash;

        // email z registrace
        // - pokud existuje registrace (loginAggregate má registration) defaultně nastaví jako email hodnotu z registrace $registration->getEmail(), pak input pro email je readonly
        // - předvyplňuje se z $visitorData
        $email = isset($visitorProfileEntity) ? $visitorProfileEntity->getEmail() : ($loginAggregate->getRegistration() ? $loginAggregate->getRegistration()->getEmail() : '');

        // hodnoty do formuláře z visitorDataPost (odeslaná data - zájem o pozici), pokud ještě nevznikl z visitorData (z profilu)
        if (isset($visitorJobRequestEntity)) {
            $isVisitorDataPost = true;
            $readonly = 'readonly="1"';
            $disabled = 'disabled="1"';
            $prefix = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getPrefix() : '';
            $email = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getEmail() : '';
            $readonlyEmail = $email ? 'readonly="1"' : '';  // proměnná pro input email

            $firstName = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getName() : '';
            $surname = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getSurname() : '';
            $postfix = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getPostfix() : '';
            $phone = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getPhone() : '';
            $cvEducationText = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getCvEducationText() : '';
            $cvSkillsText = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getCvSkillsText() : '';
                        
            $cvId = $visitorJobRequestEntity->getCvDocument();
            if (isset($cvId)) {
                /** @var DocumentInterface $documentCv */
                $documentCv = $documentRepo->get($cvId);
                $visitorFormData['cvDocumentFilename'] = $documentCv->getDocumentFilename();
            }
            $letterId = $visitorJobRequestEntity->getLetterDocument();
            if (isset($letterId)) {
                /** @var DocumentInterface $documentLetter */
                $documentLetter = $documentRepo->get($letterId);
                $visitorFormData['letterDocumentFilename'] = $documentLetter->getDocumentFilename(); 
            }
            
            
            
        } else {
            $isVisitorDataPost = false;
            $readonly = '';
            $disabled = '';
            // - pokud existuje registrace (loginAggregate má registration) defaultně nastaví jako email hodnotu z registrace $registration->getEmail(), pak input pro email je readonly
            // - předvyplňuje se z $visitorData
            $email = isset($visitorProfileEntity) ? $visitorProfileEntity->getEmail() : ($loginAggregate->getRegistration() ? $loginAggregate->getRegistration()->getEmail() : '');
            $readonlyEmail = $email ? 'readonly="1"' : '';  // proměnná pro input email

            $prefix = isset($visitorProfileEntity) ? $visitorProfileEntity->getPrefix() : '';
            $firstName = isset($visitorProfileEntity) ? $visitorProfileEntity->getName() : '';
            $surname = isset($visitorProfileEntity) ? $visitorProfileEntity->getSurname() : '';
            $postfix = isset($visitorProfileEntity) ? $visitorProfileEntity->getPostfix() : '';
            $phone = isset($visitorProfileEntity) ? $visitorProfileEntity->getPhone() : '';
            $cvEducationText = isset($visitorProfileEntity) ? $visitorProfileEntity->getCvEducationText() : '';
            $cvSkillsText = isset($visitorProfileEntity) ? $visitorProfileEntity->getCvSkillsText(): '';
                        
            $cvId = $visitorProfileEntity->getCvDocument();
            if (isset($cvId)) {
                /** @var DocumentInterface $documentCv */
                $documentCv = $documentRepo->get($cvId);
                $visitorFormData['cvDocumentFilename'] = $documentCv->getDocumentFilename();
            }                        
            $letterId = $visitorProfileEntity->getLetterDocument();
            if (isset($letterId)) {
                 /** @var DocumentInterface $documentLetter */
                $documentLetter = $documentRepo->get($letterId);
                $visitorFormData['letterDocumentFilename'] = $documentLetter->getDocumentFilename();
            
            }
                        
        }
    }

    if ($isRepresentative) {        
        $visitorJobRequests = $visitorJobRequestRepo->find(  ' job_Id = :jobId ',  [  'jobId' => $jobId  ] );
        $visitorJobRequestCount = count($visitorJobRequests);        
        $allFormVisitorDataPost = [];

        if ($visitorJobRequests) {
            $isVisitorDataPost = true;
            $visitorFormData['readonly'] = 'readonly="1"';
            $visitorFormData['disabled'] = 'disabled="1"';
            $visitorFormData['shortName'] = $shortName;
            $visitorFormData['positionName'] = $positionName;
            $visitorFormData['jobId'] = $jobId;
            
            $visitorFormData['isRepresentative'] = $isRepresentative;
            $visitorFormData['isVisitor'] = $isVisitor;
            $visitorFormData['presenterEmail'] = $loginAggregate->getRegistration() ? $loginAggregate->getRegistration()->getEmail() : 'Nezadána mail adresa!';
            /** @var VisitorJobRequestInterface $visitorJobRequestEntity */
            foreach ($visitorJobRequests as $visitorJobRequestEntity) {
                $visitorFormData['visitorLoginName'] = $visitorJobRequestEntity->getLoginLoginName();  // pro hidden pole
                $visitorFormData['prefix'] = $visitorJobRequestEntity->getPrefix();
                $visitorFormData['email'] = $visitorJobRequestEntity->getEmail();
                $visitorFormData['readonlyEmail'] = $visitorJobRequestEntity->getEmail() ? 'readonly="1"' : '';  // proměnná pro input email

                $visitorFormData['firstName'] = $visitorJobRequestEntity->getName();
                $visitorFormData['surname'] = $visitorJobRequestEntity->getSurname();
                $visitorFormData['postfix'] = $visitorJobRequestEntity->getPostfix();
                $visitorFormData['phone'] = $visitorJobRequestEntity->getPhone();
                $visitorFormData['cvEducationText'] = $visitorJobRequestEntity->getCvEducationText();
                $visitorFormData['cvSkillsText'] = $visitorJobRequestEntity->getCvSkillsText();
                                
                $cvId = $visitorJobRequestEntity->getCvDocument();
                if (isset($cvId)) {
                    /** @var DocumentInterface $documentCv */
                    $documentCv = $documentRepo->get($cvId);
                    $visitorFormData['cvDocumentFilename'] = $documentCv->getDocumentFilename();
                }                                    
                $letterId = $visitorJobRequestEntity->getLetterDocument();
                if (isset($letterId)) {
                    /** @var DocumentInterface $documentLetter */
                    $documentLetter = $documentRepo->get($letterId);
                    $visitorFormData['letterDocumentFilename'] = $documentLetter->getDocumentFilename();  
                }                
                $allFormVisitorDataPost[] = $visitorFormData;
            //    $allFormVisitorDataPost[] = array_merge ($visitorFormData);
                
            }
            //$allFormVisitorDataPostMerge = array_merge ($allFormVisitorDataPost, ['loginName' => $loginName  ]);
        }
    }
//    //*--------------------------------------------------
//    $companyContactEntities = $companyContactRepo->find( ' company_id = :companyId ',  [  'companyId' => $companyId  ] ) ;
//    $сompanyEmailsA = '';
//    /** @var CompanyContactInterface $companyContactEntity */
//    foreach ($companyContactEntities  as  $companyContactEntity) {
//        $сompanyEmails  = '';
//        $e = $companyContactEntity->getEmails();
//        $сompanyEmails  = ( $e  ) ?   $e.';'  : '' ;
//        $сompanyEmailsA =  $сompanyEmailsA . $сompanyEmails; 
//    }
//    $presenterEmails = $сompanyEmailsA;
    
     //**I** xxxxxxx pozice_2 ****
}
?>


        <div class="title">
        <!--   $isRepresentative   je reprezentantem  firmy   -->   
        <?php
            if ($isRepresentative) {  ?>
                <?= "**I** Přihlášený " . $loginName. " JE representantem " . " vystavovatele " . $shortName ?>
        <?php } else { ?>
                <?= !($isVisitor) ? "**I** Přihlášený " .$loginName. " NENÍ representantem "  . " vystavovatele " . $shortName : "" ?>
        <?php }  ?>
           
             
            
            <p class="podnadpis"><i class="dropdown icon"></i><?= $nazev ?>, <?= $mistoVykonu ?>
                <?= $this->repeat(__DIR__.'/pozice/tag_2.php', isset($kategorie) ? $kategorie : [] , 'seznam') ?>
                <?php
                if($isVisitor AND $isVisitorDataPost) {
                    ?>
                    <span class="ui big green label">Pracovní údaje odeslány</span>
                    <?php
                }
                if($isRepresentative) {
                    if ($visitorJobRequestCount>0) {
                    ?>
                    <span class="ui big orange label">Hlásí se zájemci na pozici. Počet: <?= $visitorJobRequestCount ?></span>
                    <?php
                    } else {
                    ?>
                    <span class="ui big grey label">Na pozici se dosud nikdo nehlásil.</span>
                    <?php
                    }
                }
                ?>
            </p>
        </div>
        <div class="content">
            <div class="ui grid stackable">
                <div class="row">
                    <div class="four wide column"><b>Místo výkonu práce:</b></div>
                    <div class="six wide column"><?= $mistoVykonu ?></div>
                </div>
                <div class="row">
                    <div class="four wide column"><b>Požadované vzdělání:</b></div>
                    <div class="six wide column"><?= $vzdelani ?></div>
                </div>
                <div class="row">
                    <div class="four wide column">
                        <p><b>Popis pracovní pozice:</b></p>
                    </div>
                    <div class="twelve wide column"><div><?= $popisPozice ?></div></div>
                </div>
                <div class="row">
                    <div class="eight wide column">
                        <p><b>Požadujeme:</b></p>
                        <div>
                            <?= $this->repeat(__DIR__.'/pozice/li.php', $pozadujeme, 'seznam') ?>
                        </div>
                    </div>
                    <div class="eight wide column">
                        <p><b>Nabízíme:</b></p>
                        <div>
                            <?= $this->repeat(__DIR__.'/pozice/li.php', $nabizime, 'seznam') ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="sixteen wide column">
                        <div  class="navazat-kontakt">
                            <div class="ui grid">
                                    <?php
                                    if ($isVisitor) {
                                        ?>
                                        <div class="sixteen wide column center aligned">
                                        <?php
                                        if($isVisitorDataPost) {
                                            ?>
                                            <div class="ui large button green profil-visible">
                                                <i class="play icon"></i>
                                                <span>Chci si prohlédnout údaje, které jsem odeslal/a  &nbsp;</span>
                                                <i class="play flipped icon"></i>
                                            </div>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="ui large button blue profil-visible">
                                                <i class="play icon"></i>
                                                <span>Mám zájem o tuto pozici &nbsp;</span>
                                                <i class="play flipped icon"></i>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        </div>
                                        <div class="sixteen wide column">
                                            <div class="profil hidden">
                                                <?php
                                                    // pokud je $visitorDataPosted je nastaveno readonly
                                                    include ConfigurationCache::componentController()['templates'].'visitor-data/osobni-udaje_2.php'; ?>
                                            </div>
                                        </div>
                                        <?php
                                    } elseif ($isRepresentative) {
                                        if($isVisitorDataPost) {
                                            ?>
                                            <div class="sixteen wide column center aligned">

                                                <div class="ui large button green profil-visible">
                                                    <i class="play icon"></i>
                                                    <span>Chci si prohlédnout údaje, které zájemci odeslali  &nbsp;</span>
                                                    <i class="play flipped icon"></i>
                                                </div>
                                            </div>
                                            <div class="sixteen wide column">
                                                <div class="profil hidden">
                                                    <?= $this->repeat(ConfigurationCache::componentController()['templates'].'visitor-data/osobni-udaje_2.php',
                                                                      $allFormVisitorDataPost) ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <div class="sixteen wide column center aligned">
                                            <div class="ui large button blue profil-visible">
                                                <i class="play icon"></i>
                                                <span>Mám zájem o tuto pozici &nbsp;</span>
                                                <i class="play flipped icon"></i>
                                            </div>
                                            <div class="profil hidden">
                                                <div class="active title">
                                                    <i class="exclamation icon"></i>Přihlašte se jako návštěvník. <i class="user icon"></i> Přihlášení návštěvníci mohou posílat přímo zaměstnavateli. Pokud ještě nejste zaregistrování, nejprve se registrujte. <i class="address card icon"></i>
                                                </div>
                                                <?php
                                                if (isset($block)) {
                                                    ?>
                                                    <a href="<?= "web/v1/page/block/".$block->getName()."#chci-navazat-kontakt" ?>">
                                                        <div class="ui large button grey profil-visible">
                                                            Chci jít na stánek pro kontaktní údaje
                                                        </div>
                                                    </a>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
