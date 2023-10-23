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

use Events\Model\Arraymodel\RepresenrativeViewModel;
/** @var PhpTemplateRendererInterface $this */
//  ************** pozice = job **************************
###### kontext #######
// jobs[]- expand job -> $nazev; $mistoVykonu; $vzdelani; $popisPozice; $pozadujeme; $nabizime; + přidané  $container
$positionName = $nazev;
#######################


$isPresenter = false;
$isVisitor = false;
$isVisitorDataPost = false;

// používá se: $kvalifikace[$vzdelani]
$kvalifikace = [
    1 => 'Bez omezení',
    2 => 'ZŠ',
    3 => 'SOU bez maturity',
    4 => 'SOU s maturitou',
    5 => 'SŠ',
    6 => 'VOŠ / Bc.',
    7 => 'VŠ',
];

#####
$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateFullInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();
####

/** @var RepresenrativeViewModel $representativeModel */
$representativeModel = $container->get( RepresenrativeViewModel::class );


/** @var VisitorJobRequestRepo $visitorDataPostRepo */
$visitorDataPostRepo = $container->get(VisitorJobRequestRepo::class);


if (isset($loginAggregate)) {
    $loginName = $loginAggregate->getLoginName();
    $role = $loginAggregate->getCredentials()->getRole() ?? '';
    $presenterPerson = $representativeModel->getPerson($loginName);

    $isVisitor = $role==ConfigurationCache::loginLogoutController()['roleVisitor'];
    $isPresenter = (($role==ConfigurationCache::loginLogoutController()['rolePresenter']) AND ($presenterPerson['shortName']==$shortName));

    if ($isVisitor) {
        /** @var VisitorProfileRepo $visitorDataRepo */
        $visitorDataRepo = $container->get(VisitorProfileRepo::class);
        /** @var VisitorProfileInterface $visitorData */
        $visitorData = $visitorDataRepo->get($loginName);

        /** @var VisitorJobRequestInterface $visitorDataPost */
//################################################################
        $visitorDataPost = $visitorDataPostRepo->get($loginName, $jobId) ; // tady ma byt id jobu                                
                /*$shortName, $positionName*/ //$shortName, $positionName ...job_id

        // formulář
        // unikátní jména souborů pro upload
        $userHash = $loginAggregate->getLoginNameHash();
        $accept = implode(", ", ConfigurationCache::filesUploadController()['upload.events.acceptedextensions']);
        $uploadedCvFilename = VisitorProfileControler::UPLOADED_KEY_CV.$userHash;
        $uploadedLetterFilename = VisitorProfileControler::UPLOADED_KEY_LETTER.$userHash;

        // email z registrace
        // - pokud existuje registrace (loginAggregate má registration) defaultně nastaví jako email hodnotu z registrace $registration->getEmail(), pak input pro email je readonly
        // - předvyplňuje se z $visitorData
        $email = isset($visitorData) ? $visitorData->getEmail() : ($loginAggregate->getRegistration() ? $loginAggregate->getRegistration()->getEmail() : '');

        $visitorLoginName = $loginName;

        // hodnoty do formuláře z visitorDataPost (odeslaná data - zájem o pozici), pokud ještě nevznikl z visitorData (z profilu)
        if (isset($visitorDataPost)) {
            $isVisitorDataPost = true;
            $readonly = 'readonly="1"';
            $disabled = 'disabled="1"';
            $prefix = isset($visitorDataPost) ? $visitorDataPost->getPrefix() : '';
            $email = isset($visitorDataPost) ? $visitorDataPost->getEmail() : '';
            $readonlyEmail = $email ? 'readonly="1"' : '';  // proměnná pro input email

            $firstName = isset($visitorDataPost) ? $visitorDataPost->getName() : '';
            $surname = isset($visitorDataPost) ? $visitorDataPost->getSurname() : '';
            $postfix = isset($visitorDataPost) ? $visitorDataPost->getPostfix() : '';
            $phone = isset($visitorDataPost) ? $visitorDataPost->getPhone() : '';
            $cvEducationText = isset($visitorDataPost) ? $visitorDataPost->getCvEducationText() : '';
            $cvSkillsText = isset($visitorDataPost) ? $visitorDataPost->getCvSkillsText() : '';
            $cvDocumentFilename = isset($visitorDataPost) ? $visitorDataPost->getCvDocumentFilename() : '';
            $letterDocumentFilename = isset($visitorData) ? $visitorData->getLetterDocumentFilename() : '';
        } else {
            $isVisitorDataPost = false;
            $readonly = '';
            $disabled = '';
            // - pokud existuje registrace (loginAggregate má registration) defaultně nastaví jako email hodnotu z registrace $registration->getEmail(), pak input pro email je readonly
            // - předvyplňuje se z $visitorData
            $email = isset($visitorData) ? $visitorData->getEmail() : ($loginAggregate->getRegistration() ? $loginAggregate->getRegistration()->getEmail() : '');
            $readonlyEmail = $email ? 'readonly="1"' : '';  // proměnná pro input email

            $prefix = isset($visitorData) ? $visitorData->getPrefix() : '';
            $firstName = isset($visitorData) ? $visitorData->getName() : '';
            $surname = isset($visitorData) ? $visitorData->getSurname() : '';
            $postfix = isset($visitorData) ? $visitorData->getPostfix() : '';
            $phone = isset($visitorData) ? $visitorData->getPhone() : '';
            $cvEducationText = isset($visitorData) ? $visitorData->getCvEducationText() : '';
            $cvSkillsText = isset($visitorData) ? $visitorData->getCvSkillsText(): '';
            $cvDocumentFilename = isset($visitorData) ? $visitorData->getCvDocumentFilename() : '';
            $letterDocumentFilename = isset($visitorData) ? $visitorData->getLetterDocumentFilename() : '';
        }
    }

    if ($isPresenter) {
        /** @var VisitorJobRequestInterface $visitorDataPost */
        $visitorDataPosts = $visitorDataPostRepo->findAllForPosition($shortName, $positionName);
        $visitorDataCount = count($visitorDataPosts);
        $allFormVisitorDataPost = [];

        if ($visitorDataPosts) {
            $isVisitorDataPost = true;
            $visitorFormData['readonly'] = 'readonly="1"';
            $visitorFormData['disabled'] = 'disabled="1"';
            $visitorFormData['shortName'] = $shortName;
            $visitorFormData['positionName'] = $positionName;
            $visitorFormData['isPresenter'] = $isPresenter;
            $visitorFormData['isVisitor'] = $isVisitor;
            $visitorFormData['presenterEmail'] = $loginAggregate->getRegistration() ? $loginAggregate->getRegistration()->getEmail() : 'Nezadána mail adresa!';
            foreach ($visitorDataPosts as $visitorDataPost) {
                $visitorFormData['visitorLoginName'] = $visitorDataPost->getLoginLoginName();  // pro hidden pole
                $visitorFormData['prefix'] = $visitorDataPost->getPrefix();
                $visitorFormData['email'] = $visitorDataPost->getEmail();
                $visitorFormData['readonlyEmail'] = $visitorDataPost->getEmail() ? 'readonly="1"' : '';  // proměnná pro input email

                $visitorFormData['firstName'] = $visitorDataPost->getName();
                $visitorFormData['surname'] = $visitorDataPost->getSurname();
                $visitorFormData['postfix'] = $visitorDataPost->getPostfix();
                $visitorFormData['phone'] = $visitorDataPost->getPhone();
                $visitorFormData['cvEducationText'] = $visitorDataPost->getCvEducationText();
                $visitorFormData['cvSkillsText'] = $visitorDataPost->getCvSkillsText();
                $visitorFormData['cvDocumentFilename'] = $visitorDataPost->getCvDocumentFilename();
                $visitorFormData['letterDocumentFilename'] = $visitorDataPost->getLetterDocumentFilename();
                $allFormVisitorDataPost[] = $visitorFormData;
            }
        }
    }
}
?>

        <div class="title">
            <p class="podnadpis"><i class="dropdown icon"></i><?= $nazev ?>, <?= $mistoVykonu ?>
                <?= $this->repeat(__DIR__.'/pozice/tag.php', $kategorie, 'cislo') ?>
                <?php
                if($isVisitor AND $isVisitorDataPost) {
                    ?>
                    <span class="ui big green label">Pracovní údaje odeslány</span>
                    <?php
                }
                if($isPresenter) {
                    if ($visitorDataCount>0) {
                    ?>
                    <span class="ui big orange label">Hlásí se zájemci na pozici. Počet: <?= $visitorDataCount ?></span>
                    <?php
                    } else {
                    ?>
                    <span class="ui big grey label">Na pozici se dosud nikdo nehlásil</span>
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
                    <div class="six wide column"><?= $kvalifikace[$vzdelani] ?></div>
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
                                                    include ConfigurationCache::componentController()['templates'].'visitor-data/osobni-udaje.php'; ?>
                                            </div>
                                        </div>
                                        <?php
                                    } elseif ($isPresenter) {
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
                                                    <?= $this->repeat(ConfigurationCache::componentController()['templates'].'visitor-data/osobni-udaje.php', $allFormVisitorDataPost); ?>
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