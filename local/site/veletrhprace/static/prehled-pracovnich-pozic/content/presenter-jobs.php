<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

?>
            <div>
                <p class="velky text primarni-barva tucne"><?= $presenterName ?></p>
                <?=  $this->insertIf(isset($block), __DIR__.'/content/presenter-link.php', ['block'=>$block]);  ?>
                <?= $this->insert( ConfigurationCache::componentController()['templates']."paper/presenter-job/content/vypis-pozic_2.php", $presenterJobs  ); ?>
            </div>
