<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;


/** @var PhpTemplateRendererInterface $this */

//TODO: semantic steps attached

?>
           <div class="eight wide column">
                <div class="ui segment">
                            <p  class="text primarni-barva zadne-okraje"><?php echo (isset($prefix) ? "$prefix " : "").(isset($name) ? "$name " : "").(isset($surname) ? "$surname" : "").(isset($postfix) ? ", $postfix" : "") ?></p>
                        </div>
                    </div>
                    <div class="ui horizontal segments">
                        <div class="ui segment">
                            E-mail
                        </div>
                        <div class="ui segment">
                            <?= $email ?? '';  ?>
                        </div>
                    </div>
                    <div class="ui horizontal segments">
                        <div class="ui segment">
                            Telefon
                        </div>
                        <div class="ui segment">
                            <?= $phone ?? '';  ?>
                        </div>
                    </div>
                    <div class="ui segment">
                        <p>Vzdělání, kurzy</p>
                        <div>
                            <?= $cvEducationText ?? ''; ?>
                        </div>
                    </div>
                    <div class="ui segment">
                        <p>Pracovní zkušenosti, dovednosti</p>
                        <div>
                            <?= $cvSkillsText ?? ''; ?>
                        </div>
                    </div>
                                         