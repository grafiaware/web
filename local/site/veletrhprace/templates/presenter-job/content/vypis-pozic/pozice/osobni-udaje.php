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

#####
$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateCredentialsInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();

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
    }
    
    
?>


            <div class="active title">
                <i class="dropdown icon"></i>
                Balíček pracovních údajů
            </div>
            <div class="active content">
                <form class="ui huge form" action="api/v1/event/visitorpost" method="POST">
                    <input type='hidden' name="short-name" value="<?= $shortName ?>">
                    <input type='hidden' name="position-name" value="<?= $positionName ?>">
                    <div class="four fields">
                        <div class="three wide field">
                            <label>Titul před jménem</label>
                            <input <?= $readonly ?> type="text" name="prefix" placeholder="" maxlength="45" value="<?= $prefix ?>">
                        </div>
                        <div class="five wide field">
                            <label>Jméno</label>
                            <input <?= $readonly ?> type="text" name="name" placeholder="Jméno" maxlength="90" value="<?= $firstName ?>">
                        </div>
                        <div class="five wide field">
                            <label>Příjmení</label>
                            <input <?= $readonly ?> type="text" name="surname" placeholder="Příjmení" maxlength="90" value="<?= $surname ?>">
                        </div>
                        <div class="three wide field">
                            <label>Titul za jménem</label>
                            <input <?= $readonly ?> type="text" name="postfix" placeholder="" maxlength="45" value="<?= $postfix ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>E-mail</label>
                            <input <?= $readonly ?> type="email" name="email" placeholder="mail@example.cz" maxlength="90" value="<?= $email ?>">
                        </div>
                        <div class="field">
                            <label>Telefon</label>
                            <input <?= $readonly ?> type="tel" name="phone" placeholder="+420 777 8888 555" pattern="(\+420)\s[1-9]\d{2}\s\d{3}\s\d{3}" maxlength="45" value="<?= $phone ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Vzdělání, kurzy</label>
                            <textarea <?= $disabled ?> name="cv-education-text" class="working-data"><?= $cvEducationText ?></textarea>
                        </div>
                        <div class="field">
                            <label>Pracovní zkušenosti, dovednosti</label>
                            <textarea <?= $disabled ?> name="cv-skills-text" class="working-data"><?= $cvSkillsText ?></textarea>
                        </div>
                    </div>

                    <label><b>Nahrané soubory</b></label>
                    <div class="two fields">
                        <div class="field">
                            <p>Životopis: <?= isset($visitorData) ? $visitorData->getCvDocumentFilename() : ''; ?></p>
                            <p>Motivační dopis: <?= isset($visitorData) ? $visitorData->getLetterDocumentFilename() : ''; ?></p>
                        </div>
                        <?php
                        if($readonly === '') {
                        ?>
                        <div class="field">
                            <button class="ui massive primary button" type="submit">Odeslat</button>
                        </div>
                        <?php
                        }
                        ?>
                    </div>

                </form>
            </div>
<?php

} else {

?>
                <div class="active title">
                    <i class="exclamation icon"></i>
                    Přihlašte se. Údaje ze svého profilu mohou posílat přihlášení uživatelé.
                </div>

<?php

}

?>