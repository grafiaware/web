<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;


//use Site\ConfigurationCache;
//use Access\Enum\RoleEnum;
//
//use Status\Model\Repository\StatusSecurityRepo;
//use Status\Model\Entity\StatusSecurityInterface;
//
//use Component\ViewModel\StatusViewModel;
//use Component\ViewModel\StatusViewModelInterface;
//
//use Auth\Model\Entity\LoginAggregateFullInterface;
//
//use Events\Middleware\Events\Controler\VisitorProfileControler;
//
//use Events\Model\Repository\VisitorProfileRepo;
//use Events\Model\Entity\VisitorProfileInterface;
//use Events\Model\Repository\VisitorJobRequestRepo;
//use Events\Model\Entity\VisitorJobRequestInterface;
//use Events\Model\Repository\DocumentRepo;
//use Events\Model\Entity\DocumentInterface;
//use Events\Model\Repository\RepresentativeRepo;

/** @var PhpTemplateRendererInterface $this */

###### kontext #######
// jobs expand -> jobId, companyId, $nazevPozice; $mistoVykonu; $vzdelani; $popisPozice; $pozadujeme; $nabizime; + přidané $container

$isRepresentativeOfCompany = false;
$isVisitor = false;
$isVisitorDataPost = false;



####
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityInterface $statusSecurity */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateFullInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();
####

/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$representativeActions = $statusViewModel->getRepresentativeActions();
$representativeFromStatus = isset($representativeActions) ? $representativeActions->getRepresentative() : null;


/** @var VisitorJobRequestRepo $visitorJobRequestRepo */
$visitorJobRequestRepo = $container->get(VisitorJobRequestRepo::class);
/** @var DocumentRepo $documentRepo */
$documentRepo = $container->get(DocumentRepo::class);




if (isset($loginAggregate)) {
    $loginName = $loginAggregate->getLoginName();
    $registation = $loginAggregate->getRegistration();
    $loginEmailFromRegistration = isset($registation) ? $registation->getEmail() : "";  // promenou nepotrebuji, ale email ano cca radek 180
    
    $role = $loginAggregate->getCredentials()->getRoleFk() ?? '';

    //*--------------------------------
    $isVisitor = $role==RoleEnum::VISITOR;   
    
    $isRepresentativeOfCompany = (isset($representativeFromStatus) AND $representativeFromStatus->getCompanyId()==$companyId);
//------------------------------------------------------------------------------------------------------------------------
    
    
    if ($isVisitor) {
        /** @var VisitorProfileRepo $visitorProfileRepo */
        $visitorProfileRepo = $container->get(VisitorProfileRepo::class);
        /** @var VisitorProfileInterface $visitorProfileEntity */
        $visitorProfileEntity = $visitorProfileRepo->get($loginName);

        /** @var VisitorJobRequestInterface $visitorJobRequestEntity */
        $visitorJobRequestEntity = $visitorJobRequestRepo->get($loginName, $jobId) ;         // jen 1               

        // formulář
        // unikátní jména souborů pro upload
        $userHash = $loginAggregate->getLoginNameHash();
        $accept = implode(", ", ConfigurationCache::eventsUploads()['upload.events.acceptedextensions']);
        $uploadedCvFilename = VisitorProfileControler::UPLOADED_KEY_CV.$userHash;
        $uploadedLetterFilename = VisitorProfileControler::UPLOADED_KEY_LETTER.$userHash;

        // email z registrace
        // - pokud existuje registrace (loginAggregate má registration) defaultně nastaví jako email hodnotu z registrace $registration->getEmail(), pak input pro email je readonly
        // - předvyplňuje se z $visitorData
        $email = isset($visitorProfileEntity) ? $visitorProfileEntity->getEmail() : ($loginAggregate->getRegistration() ? $loginAggregate->getRegistration()->getEmail() : '');

        // hodnoty do formuláře z visitorDataPost (odeslaná data - zájem o pozici), pokud ještě nevznikl z visitorData (z profilu)
        if (isset($visitorJobRequestEntity)) {
            $isVisitorDataPost = true;
           
                    $visitorFormData ['readonly']  = 'readonly="1"';
                    $visitorFormData ['disabled'] = 'disabled="1"';
                    $visitorFormData ['prefix'] = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getPrefix() : '';
                    $visitorFormData ['email'] = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getEmail() : '';
                    $visitorFormData ['readonlyEmail'] = $email ? 'readonly="1"' : '';  // proměnná pro input email

                    $visitorFormData ['firstName'] = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getName() : '';
                    $visitorFormData ['surname'] = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getSurname() : '';
                    $visitorFormData ['postfix'] = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getPostfix() : '';
                    $visitorFormData ['phone'] = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getPhone() : '';
                    $visitorFormData ['cvEducationText'] = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getCvEducationText() : '';
                    $visitorFormData ['cvSkillsText'] = isset($visitorJobRequestEntity) ? $visitorJobRequestEntity->getCvSkillsText() : '';
                        
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
            
            
                // nema ulozene zadosti  - bere z profilu
        } else {
            $isVisitorDataPost = false;
           
                    $visitorFormData ['$readonly'] = '';
                    $visitorFormData ['$disabled'] = '';
                    // - pokud existuje registrace (loginAggregate má registration) defaultně nastaví jako email hodnotu z registrace $registration->getEmail(), pak input pro email je readonly
                    // - předvyplňuje se z $visitorData
                    $visitorFormData ['$email'] = isset($visitorProfileEntity) ? $visitorProfileEntity->getEmail() : ($loginAggregate->getRegistration() ? $loginAggregate->getRegistration()->getEmail() : '');
                    $visitorFormData ['$readonlyEmail'] = $email ? 'readonly="1"' : '';  // proměnná pro input email

                    $visitorFormData ['$prefix'] = isset($visitorProfileEntity) ? $visitorProfileEntity->getPrefix() : '';
                    $visitorFormData ['$firstName'] = isset($visitorProfileEntity) ? $visitorProfileEntity->getName() : '';
                    $visitorFormData ['$surname'] = isset($visitorProfileEntity) ? $visitorProfileEntity->getSurname() : '';
                    $visitorFormData ['$postfix'] = isset($visitorProfileEntity) ? $visitorProfileEntity->getPostfix() : '';
                    $visitorFormData ['$phone'] = isset($visitorProfileEntity) ? $visitorProfileEntity->getPhone() : '';
                    $visitorFormData ['$cvEducationText'] = isset($visitorProfileEntity) ? $visitorProfileEntity->getCvEducationText() : '';
                    $visitorFormData ['$cvSkillsText'] = isset($visitorProfileEntity) ? $visitorProfileEntity->getCvSkillsText(): '';
                        
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

    if ($isRepresentativeOfCompany) {        
        //vsechny zadosti o práci $jobId
        $visitorJobRequests = $visitorJobRequestRepo->find(  ' job_Id = :jobId ',  [  'jobId' => $jobId  ] );
        $visitorJobRequestCount = count($visitorJobRequests);        
        $allFormVisitorDataPost = [];

        if ($visitorJobRequests) {
            $isVisitorDataPost = true;
            $visitorFormData['readonly'] = 'readonly="1"';
            $visitorFormData['disabled'] = 'disabled="1"';
          //  $visitorFormData['shortName'] =  $nameCompany;   //$shortName;
            $visitorFormData['positionName'] = $nazevPozice;  //$nazev
            $visitorFormData['jobId'] = $jobId;
            
            $visitorFormData['isRepresentativeOfCompany'] = $isRepresentativeOfCompany;
            $visitorFormData['isVisitor'] = $isVisitor;
            
            //doufam ze nepotebny email v osobni-udaje.php ... presenter neni, presenter je  company  
            $visitorFormData['presenterEmail'] = $loginAggregate->getRegistration() ? $loginAggregate->getRegistration()->getEmail() : 'Nezadána mail adresa presentera!';
            //potrebny email v osobni-udaje.php   
            $visitorFormData['loginEmailFromRegistration'] = $loginAggregate->getRegistration() ? $loginAggregate->getRegistration()->getEmail() :
                                                                                              'Nezadána mail.adresa representanta!';
 
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
            }
            //$allFormVisitorDataPostMerge = array_merge ($allFormVisitorDataPost, ['loginName' => $loginName  ]);
        }
    }
}
    
?>

    <div class="ui styled fluid accordion">   
       
        <div class="title">
            <i class="dropdown icon"></i> 
                <?= $nazev ?? '' ?> 
                <span class="ui big yellow label"><i class="bell outline icon"></i>Neodeslané na mail. Počet: 3</span>
            <?php /*
    //----------
                if($isVisitor AND $isVisitorDataPost) {
                    ?>
                    <span class="ui big green label">Pracovní údaje odeslány</span>
                    <?php
                }
    //----------                
                if($isRepresentativeOfCompany) {
                    if ($visitorJobRequestCount>0) {
                    ?>
                    <span class="ui big orange label">Hlásí se zájemci na pozici. Počet: <?= $visitorJobRequestCount ?></span>
                    
                    <span class="ui big orange label">Neodeslané na mail. Počet: 3</span>
                    <?php
                    } else {
                    ?>
                    <span class="ui big grey label">Na pozici se dosud nikdo nehlásil.</span>
                    <?php
                    }
                }
//----------
                */ ?>
                    
        </div>
        <div class="content">
            <div class="ui grid">       
                <div class="sixteen wide column">
                    <?php
                        echo Html::tag('span', 
                            [
                                'class'=>'cascade',
                                'data-red-apiuri'=>"events/v1/data/job/$id/jobtotag",
                            ]
                        );     
                    ?>                 
                </div>
                <div class="five wide column">
                    <p><b>Místo výkonu:</b></p>
                </div>
                <div class="ten wide column">
                    <p><?= $mistoVykonu ?? '' ?></p>
                </div>
                <div class="five wide column">
                    <p><b>Požadované vzdělání: </b></p>
                </div>
                <div class="ten wide column">
                    <p><?= $selectEducations [$pozadovaneVzdelaniStupen] ?? '' ?></p>
                </div>               
                <div class="sixteen wide column">
                    <p class="text tucne zadne-okraje">Popis pozice:</p>
                    <p><?= $popisPozice ?? '' ?></p>
                </div>                
                <div class="eight wide column">
                    <p class="text tucne zadne-okraje">Požadujeme:</p>
                    <p><?= $pozadujeme ?? '' ?></p>
                </div>                 
                <div class="eight wide column">
                    <p class="text tucne zadne-okraje">Nabízíme:</p>
                    <p><?= $nabizime ?? '' ?></p>
                </div>
                <div class="sixteen wide column">
                        <div  class="navazat-kontakt">
                            <div class="ui grid">
                                <?php /*
//----------                               
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
                                                // pokud je $visitorDataPosted je nastaveno readonly  ?????
//                                                include ConfigurationCache::eventTemplates()['templates'].'visitor-data/osobni-udaje.php'; 
                                            ?>
                                            <?= $this->insert( ConfigurationCache::eventTemplates()['templates'].'visitor-data/osobni-udaje.php',
                                                                                                                  $visitorFormData  ); ?>
                                        </div>
                                    </div>
                               
                                    <?php
    //----------                                     
                                } elseif ($isRepresentativeOfCompany) {
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
                                                <?= $this->repeat(ConfigurationCache::eventTemplates()['templates'].'visitor-data/osobni-udaje.php',
                                                                                                                    $allFormVisitorDataPost) ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
//----------                                   
                                } elseif (!isset($loginAggregate)) {
                                    ?>
                                    <div class="sixteen wide column center aligned">
                                        <div class="ui large button blue profil-visible">
                                            <i class="play icon"></i>
                                            <span>Mám zájem o tuto pozici 341 &nbsp;</span>
                                            <i class="play flipped icon"></i>
                                        </div>
                                        <div class="profil hidden">
                                            <div class="active title">
                                                <i class="exclamation icon"></i>Přihlaste se jako návštěvník. <i class="user icon"></i> Přihlášení návštěvníci mohou posílat přímo zaměstnavateli. Pokud ještě nejste zaregistrování, nejprve se registrujte. <i class="address card icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                */ ?>
                            </div>
                        </div>
                    </div>
            </div>
        </div>   
    </div>        




