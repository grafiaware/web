<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;


/** @var PhpTemplateRendererInterface $this */

?>
            <div class="active title">
                <i class="dropdown icon"></i>
                Zájem o pracovní pozici
            </div>
            <div class="active content">
                <form class="ui huge form" action="events/v1/visitor" method="POST">
                    <div class="four fields">
                        <div class="three wide field">
                            <label>Titul před</label>
                            <input   type="text" name="prefix" placeholder="" maxlength="45" value="<?= $prefix ?? ''; ?>">
                        </div>
                        <div class="five wide field">
                            <label>Jméno</label>
                            <input  type="text" name="name" placeholder="Jméno" maxlength="90" value="<?= $name ?? ''; ?>">
                        </div>
                        <div class="five wide field">
                            <label>Příjmení</label>
                            <input  type="text" name="surname" placeholder="Příjmení" maxlength="90" value="<?= $surname ?? ''; ?>">
                        </div>
                        <div class="three wide field">
                            <label>Titul za</label>
                            <input  type="text" name="postfix" placeholder="" maxlength="45" value="<?= $postfix ?? ''; ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>E-mail (zde nelze zadat/opravit)</label>
                            <input type="email" name="email" placeholder="email" readonly value="<?= $email ?? '';  ?>">
                        </div>
                        <div class="field">
                            <label>Telefon</label>
                            <input  type="tel" name="phone" placeholder="+420 777 888 555" pattern="[+]?[0-9 ]{9,17}" maxlength="45" value="<?= $phone ?? ''; ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Vzdělání, kurzy</label>
                            <textarea name="cv-education-text" class="edit-userinput"><?= $cvEducationText ?? ''; ?></textarea>
                        </div>
                        <div class="field margin">
                            <label>Pracovní zkušenosti, dovednosti</label>
                            <textarea name="cv-skills-text" class="edit-userinput"><?= $cvSkillsText ?? ''; ?></textarea>
                        </div>
                    </div>

         

                                                                      