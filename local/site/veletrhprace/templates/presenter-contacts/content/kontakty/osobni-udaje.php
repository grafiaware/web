<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

use Site\Configuration;
use Model\Repository\StatusSecurityRepo;

use Middleware\Api\ApiController\VisitorDataUploadControler;
use Model\Repository\VisitorDataRepo;
use Model\Entity\VisitorData;

/** @var PhpTemplateRendererInterface $this */
/** @var VisitorData $visitorData */

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
    $visitorDataRepo = $container->get(VisitorDataRepo::class);
    $visitorData = $visitorDataRepo->get($loginName);


######################

$userHash = $loginAggregate->getLoginNameHash();
$accept = implode(", ", Configuration::filesUploadControler()['uploads.acceptedextensions']);
$nameCv = VisitorDataUploadControler::UPLOADED_KEY_CV.$userHash;
$nameLetter = VisitorDataUploadControler::UPLOADED_KEY_LETTER.$userHash;
$shortName;
?>


            <div class="active title">
                <i class="dropdown icon"></i>
                Balíček pracovních údajů
            </div>
            <div class="active content">
                <form class="ui huge form" action="api/v1/event/visitorpost" method="POST">
                    <input type='hidden' name="short-name" value="<?= $shortName ?>">
                    <div class="four fields">
                        <div class="three wide field">
                            <label>Titul před jménem</label>
                            <input disabled type="text" name="prefix" placeholder="" maxlength="45" value="<?= isset($visitorData) ? $visitorData->getPrefix() : ''; ?>">
                        </div>
                        <div class="five wide field">
                            <label>Jméno</label>
                            <input disabled type="text" name="name" placeholder="Jméno" maxlength="90" value="<?= isset($visitorData) ? $visitorData->getName() : ''; ?>">
                        </div>
                        <div class="five wide field">
                            <label>Příjmení</label>
                            <input disabled type="text" name="surname" placeholder="Příjmení" maxlength="90" value="<?= isset($visitorData) ? $visitorData->getSurname() : ''; ?>">
                        </div>
                        <div class="three wide field">
                            <label>Titul za jménem</label>
                            <input disabled type="text" name="postfix" placeholder="" maxlength="45" value="<?= isset($visitorData) ? $visitorData->getPostfix() : ''; ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>E-mail</label>
                            <input disabled type="email" name="email" placeholder="mail@example.cz" maxlength="90" value="<?= isset($visitorData) ? $visitorData->getEmail() : ''; ?>">
                        </div>
                        <div class="field">
                            <label>Telefon</label>
                            <input disabled type="tel" name="phone" placeholder="+420 777 888 555" pattern="(\+420)\s[1-9]\d{2}\s\d{3}\s\d{3}" maxlength="45" value="<?= isset($visitorData) ? $visitorData->getPhone() : ''; ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Vzdělání, kurzy</label>
                            <textarea disabled name="cv-education-text" class="working-data"><?= isset($visitorData) ? $visitorData->getCvEducationText() : ''; ?></textarea>
                        </div>
                        <div class="field margin">
                            <label>Pracovní zkušenosti, dovednosti</label>
                            <textarea disabled name="cv-skills-text" class="working-data"><?= isset($visitorData) ? $visitorData->getCvSkillsText() : ''; ?></textarea>
                        </div>
                    </div>

                    <label><b>Nahrané soubory</b></label>
                    <div class="two fields">
                        <div class="field margin">
                            <p>Životopis: <?= isset($visitorData) ? $visitorData->getCvDocumentFilename() : ''; ?></p>
                            <p>Motivační dopis: <?= isset($visitorData) ? $visitorData->getLetterDocumentFilename() : ''; ?></p>
                        </div>
                        <div class="field">
                            <div class="field">
                                <button class="ui massive primary button" type="submit">Odeslat</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
<?php

}

?>
