<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

use Site\Configuration;
use Model\Repository\StatusSecurityRepo;

use Middleware\Api\ApiController\VisitorDataUploadControler;
use Model\Repository\VisitorDataRepo;
use Model\Entity\VisitorDataInterface;
use Model\Repository\VisitorDataPostRepo;
use Model\Entity\VisitorDataPostInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

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
/** @var LoginAggregateCredentialsInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();

$positionName = $nazev;
$readonly = '';
$disabled = '';

if (isset($loginAggregate)) {
    $loginName = $loginAggregate->getLoginName();
    $role = $loginAggregate->getCredentials()->getRole() ?? '';
    $personalData['userHash'] = $loginAggregate->getLoginNameHash();
    /** @var VisitorDataRepo $visitorDataRepo */
    $visitorDataRepo = $container->get(VisitorDataRepo::class);
    /** @var VisitorDataInterface $visitorData */
    $visitorData = $visitorDataRepo->get($loginName);

    /** @var VisitorDataPostRepo $visitorDataPostRepo */
    $visitorDataPostRepo = $container->get(VisitorDataPostRepo::class);
    /** @var VisitorDataPostInterface $visitorDataPost */
    $visitorDataPost = $visitorDataPostRepo->get($loginName, $shortName, $positionName);

    $userHash = $loginAggregate->getLoginNameHash();
    $accept = implode(", ", Configuration::filesUploadControler()['uploads.acceptedextensions']);
    $nameCv = VisitorDataUploadControler::UPLOADED_KEY_CV.$userHash;
    $nameLetter = VisitorDataUploadControler::UPLOADED_KEY_LETTER.$userHash;
    

    
    
    if (isset($visitorDataPost)) {
        $readonly = 'readonly="1"';
        $disabled = 'disabled="1"';
        $prefix = isset($visitorDataPost) ? $visitorDataPost->getPrefix() : '';
        $firstName = isset($visitorDataPost) ? $visitorDataPost->getName() : '';
        $surname = isset($visitorDataPost) ? $visitorDataPost->getSurname() : ''; 
        $postfix = isset($visitorDataPost) ? $visitorDataPost->getPostfix() : '';
        $email = isset($visitorDataPost) ? $visitorDataPost->getEmail() : ''; 
        $phone = isset($visitorDataPost) ? $visitorDataPost->getPhone() : '';
        $cvEducationText = isset($visitorDataPost) ? $visitorDataPost->getCvEducationText() : '';
        $cvSkillsText = isset($visitorDataPost) ? $visitorDataPost->getCvSkillsText() : '';
        $cvDocumentFilename = isset($visitorDataPost) ? $visitorDataPost->getCvDocumentFilename() : '';
        $letterDocumentFilename = isset($visitorData) ? $visitorData->getLetterDocumentFilename() : '';
        
    } else {
        $readonly = '';
        $disabled = '';
        $prefix = isset($visitorData) ? $visitorData->getPrefix() : '';  
        $firstName = isset($visitorData) ? $visitorData->getName() : '';
        $surname = isset($visitorData) ? $visitorData->getSurname() : ''; 
        $postfix = isset($visitorData) ? $visitorData->getPostfix() : '';
        $email = isset($visitorData) ? $visitorData->getEmail() : ''; 
        $phone = isset($visitorData) ? $visitorData->getPhone() : '';
        $cvEducationText = isset($visitorData) ? $visitorData->getCvEducationText() : '';
        $cvSkillsText = isset($visitorData) ? $visitorData->getCvSkillsText(): '';
        $cvDocumentFilename = isset($visitorData) ? $visitorData->getCvDocumentFilename() : '';
        $letterDocumentFilename = isset($visitorData) ? $visitorData->getLetterDocumentFilename() : '';
    }
}
?>

        <div class="title">
            <p class="podnadpis"><i class="dropdown icon"></i><?= $nazev ?>, <?= $mistoVykonu ?>
                <?= $this->repeat(__DIR__.'/pozice/tag.php', $kategorie, 'cislo') ?>
                <?php
                if($readonly === 'readonly="1"') {
                ?>
                    <span class="ui big green label">Životopis odeslán</span>
                <?php
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
                                <div class="sixteen wide column center aligned">
                                    <?php
                                    if($readonly === 'readonly="1"') {
                                    ?>
                                    <div class="ui large button green profil-visible">
                                        <i class="play icon"></i>
                                        <span>Chci si prohlédnout údaje, které jsem odeslal/a  &nbsp;</span>
                                        <i class="play flipped icon"></i>
                                    </div>
                                    <?php
                                    } 
                                    else {
                                    ?>
                                    <div class="ui large button blue profil-visible">
                                        <i class="play icon"></i>
                                        <span>Mám zájem o tuto pozici, chci odeslat mé údaje zaměstnavateli &nbsp;</span>
                                        <i class="play flipped icon"></i>
                                    </div>
                                    <?php
                                    } 
                                    ?>
                                </div>
                                <div class="sixteen wide column">
                                    <div class="profil hidden">
                                        <?php 
                                        if (isset($loginAggregate)) {
                                            include 'pozice/osobni-udaje.php';
                                        }
                                        else {
                                        ?>
                                        <div class="active title">
                                            <i class="exclamation icon"></i>
                                            Přihlašte se. Údaje ze svého profilu mohou posílat přihlášení uživatelé.
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
            </div>
        </div>