<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;


/** @var PhpTemplateRendererInterface $this */

//TODO: semantic steps attached

?>
           <div class="ui internally celled grid">
               <div class="row">
                    <div class="sixteen wide column">
                            <p  class="text velky primarni-barva zadne-okraje"><?php echo (isset($prefix) ? "$prefix " : "").(isset($name) ? "$name " : "").(isset($surname) ? "$surname" : "").(isset($postfix) ? ", $postfix" : "") ?></p>
                    </div>
               </div>
               <div class="row">
                    <div class="eight wide column">
                        <b>E-mail</b>
                        <div>
                            <?= $email ?? '';  ?>
                        </div>
                    </div>
                    <div class="eight wide column">
                        <b>Telefon</b>
                        <div>
                            <?= $phone ?? '';  ?>
                        </div>
                    </div>
               </div>
               <div class="row">
                    <div class="eight wide column">
                        <b>Vzdělání, kurzy</b>
                        <div>
                            <?= $cvEducationText ?? ''; ?>
                        </div>
                    </div>
                    <div class="eight wide column">
                        <b>Pracovní zkušenosti, dovednosti</b>
                        <div>
                            <?= $cvSkillsText ?? ''; ?>
                        </div>
                    </div>
               </div>
            </div>
                                         