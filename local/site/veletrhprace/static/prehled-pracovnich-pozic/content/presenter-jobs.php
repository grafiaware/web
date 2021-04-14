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
                    <a href="<?= "www/block/".$block->getName()."#chci-navazat-kontakt" ?>">
                        <div class="ui button grey">
                            Chci jít na stánek pro kontaktní údaje
                        </div>
                    </a>
                    <?php
                }
                ?>


                <?= $this->insert( Configuration::componentControler()['templates']."presenter-job/content/vypis-pozic.php", $presenterJobs); ?>
            </div>
