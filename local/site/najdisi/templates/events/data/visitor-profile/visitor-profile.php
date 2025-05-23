<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;


/** @var PhpTemplateRendererInterface $this */
?>
            <div class="field"> 
                <p class="text velky primarni-barva">Balíček pracovních údajů</p>
            </div>
            <div class="four fields">
                <div class="three wide field">
                    <label>Titul před</label>
                    <p><?= isset($prefix) ? $prefix : ''; ?></p>
                </div>
                <div class="five wide field">
                    <label>Jméno</label>
                    <p><?= isset($name) ? $name : ''; ?></p>
                </div>
                <div class="five wide field">
                    <label>Příjmení</label>
                    <p><?= isset($surname) ? $surname : ''; ?></p>
                </div>
                <div class="three wide field">
                    <label>Titul za</label>
                    <p><?= isset($postfix) ? $postfix : ''; ?></p>
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>E-mail (zde nelze zadat/opravit)</label>
                    <!--<input readonly  type="email" name="email" placeholder="mail@example.cz" maxlength="90"  value=" -->
                    <p><?=  isset($visitorEmail) ? $visitorEmail : '';  ?></p>
                </div>
                <div class="field">
                    <label>Telefon</label>
                    <p><?= isset($phone) ? $phone : ''; ?></p>
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>Vzdělání, kurzy</label>
                    <textarea><?= isset($cvEducationText) ? $cvEducationText : ''; ?></textarea>
                </div>
                <div class="field">
                    <label>Pracovní zkušenosti, dovednosti</label>
                    <textarea><?= isset($cvSkillsText) ? $cvSkillsText : ''; ?></textarea>
                </div>
            </div>

         

                                                                      