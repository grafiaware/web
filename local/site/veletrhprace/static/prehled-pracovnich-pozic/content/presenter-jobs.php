<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\Configuration;

?>
            <div id="<?= $shortName ?>">
                <p class="velky text primarni-barva tucne"><?= $presenterName ?></p>

                <?php
                if (isset($block)) {
                    ?>
                    <div class="text okraje-vertical">
                        <a class="ui large button grey" href="<?= "web/v1/page/block/".$block->getName()."#chci-navazat-kontakt" ?>">
                                Chci jít na stánek pro kontaktní údaje
                        </a>
                    </div>
                    <?php
                }
                ?>


                <?= $this->insert( Configuration::componentController()['templates']."presenter-job/content/vypis-pozic.php", $presenterJobs); ?>
            </div>
