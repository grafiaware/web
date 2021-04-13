<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\Configuration;

?>
            <div id="<?= $shortName ?>">
                <p class="velky text primarni-barva tucne"><?= $presenterName ?></p>
                <?= $this->insert( Configuration::componentControler()['templates']."presenter-job/content/vypis-pozic.php", $presenterJobs); ?>
            </div>
