<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;


/** @var PhpTemplateRendererInterface $this */

?>
            <div class="field">
                <p class="text velky primarni-barva">Balíček pracovních údajů</p>
            </div>
            <!--<form class="ui huge form" action="events/v1/visitor" method="POST">-->
            <div class="four fields">
                <div class="three wide field">
                    <label>Titul před</label>
                    <input type="text" name="prefix" placeholder="" maxlength="45" value="<?= $prefix ?? ''; ?>">
                </div>
                <div class="five wide field">
                    <label>Jméno</label>
                    <input required type="text" name="name" placeholder="Jméno" maxlength="90" value="<?= $name ?? ''; ?>">
                </div>
                <div class="five wide field">
                    <label>Příjmení</label>
                    <input required type="text" name="surname" placeholder="Příjmení" maxlength="90" value="<?= $surname ?? ''; ?>">
                </div>
                <div class="three wide field">
                    <label>Titul za</label>
                    <input  type="text" name="postfix" placeholder="" maxlength="45" value="<?= $postfix ?? ''; ?>">
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>E-mail (zde nelze zadat/opravit)</label>
                    <p><?= $visitorEmail ?? '';  ?></p>
                </div>
                <div class="field">
                    <label>Telefon</label>
                    <input  type="tel" name="phone" placeholder="+420 777 888 555" pattern="(\+420)\s[1-9]\d{2}\s\d{3}\s\d{3}" maxlength="45" value="<?= $phone ?? ''; ?>">
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>Vzdělání, kurzy</label>
                    <textarea name="cv-education-text" class="edit-userinput"><?= $cvEducationText ?? ''; ?></textarea>
                </div>
                <div class="field">
                    <label>Pracovní zkušenosti, dovednosti</label>
                    <textarea name="cv-skills-text" class="edit-userinput"><?= $cvSkillsText ?? ''; ?></textarea>
                </div>
            </div>

         

                                                                      