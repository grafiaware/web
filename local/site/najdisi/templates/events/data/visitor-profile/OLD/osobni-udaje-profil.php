<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;


/** @var PhpTemplateRendererInterface $this */
    if ($editable) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }   


?>
            <div class="active title">
                <i class="dropdown icon"></i>
                Balíček pracovních údajů
            </div>
            <div class="active content">
                <form class="ui huge form" action="events/v1/visitor" method="POST">
                    <div class="four fields">
                        <div class="three wide field">
                            <label>Titul před jménem</label>
                            <input <?= $readonly ?>  type="text" name="prefix" placeholder="" maxlength="45" value="<?= isset($prefix) ? $prefix : ''; ?>">
                        </div>
                        <div class="five wide field">
                            <label>Jméno</label>
                            <input <?= $readonly ?> type="text" name="name" placeholder="Jméno" maxlength="90" value="<?= isset($name) ? $name : ''; ?>">
                        </div>
                        <div class="five wide field">
                            <label>Příjmení</label>
                            <input <?= $readonly ?> type="text" name="surname" placeholder="Příjmení" maxlength="90" value="<?= isset($surname) ? $surname : ''; ?>">
                        </div>
                        <div class="three wide field">
                            <label>Titul za jménem</label>
                            <input <?= $readonly ?> type="text" name="postfix" placeholder="" maxlength="45" value="<?= isset($postfix) ? $postfix : ''; ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>E-mail (zde nelze zadat/opravit)</label>
                            <!--<input readonly  type="email" name="email" placeholder="mail@example.cz" maxlength="90"  value=" -->
                            <?=  isset($visitorEmail) ? $visitorEmail : '';  ?>
                        </div>
                        <div class="field">
                            <label>Telefon</label>
                            <input <?= $readonly ?> type="tel" name="phone" placeholder="+420 777 888 555" pattern="(\+420)\s[1-9]\d{2}\s\d{3}\s\d{3}" maxlength="45" value="<?= isset($phone) ? $phone : ''; ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Vzdělání, kurzy</label>
                            <textarea <?= $disabled ?> name="cv-education-text" class="edit-userinput"><?= isset($cvEducationText) ? $cvEducationText : ''; ?></textarea>
                        </div>
                        <div class="field margin">
                            <label>Pracovní zkušenosti, dovednosti</label>
                            <textarea <?= $disabled ?> name="cv-skills-text" class="edit-userinput"><?= isset($cvSkillsText) ? $cvSkillsText : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <?php 
                        if ($editable) {                    
                    ?>        
                            <div class="field margin">
                                <button class="ui primary button" type="submit">Uložit údaje</button>
                            </div>
                    <?php    
                        }
                    ?>
                </form>
         

                                                                      