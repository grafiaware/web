<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;


/** @var PhpTemplateRendererInterface $this */

//TODO: semantic steps attached

?>
           <div class="ui internally celled grid">
               <div class="row">
                    <div class="sixteen wide column">
                            <p  class="text primarni-barva zadne-okraje"><?php echo (isset($prefix) ? "$prefix " : "").(isset($name) ? "$name " : "").(isset($surname) ? "$surname" : "").(isset($postfix) ? ", $postfix" : "") ?></p>
                    </div>
               </div>
               <div class="row">
                    <div class="eight wide column">
                        E-mail
                    </div>
                    <div class="eight wide column">
                        <?= $email ?? '';  ?>
                    </div>
               </div>
               <div class="row">
                    <div class="eight wide column">
                        Telefon
                    </div>
                    <div class="eight wide column">
                        <?= $phone ?? '';  ?>
                    </div>
               </div>
               <div class="row">
                    <div class="sixteen wide column">
                        <p>Vzdělání, kurzy</p>
                        <div>
                            <?= $cvEducationText ?? ''; ?>
                        </div>
                    </div>
               </div>
               <div class="row">
                    <div class="sixteen wide column">
                        <p>Pracovní zkušenosti, dovednosti</p>
                        <div>
                            <?= $cvSkillsText ?? ''; ?>
                        </div>
                    </div>
                </div>
            </div>
                                         