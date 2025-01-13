<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

?>
            <div>
                <p class="velky text primarni-barva tucne"><?= $companyName ?></p>
                <?=  $this->insertIf(isset($block), __DIR__.'/content/presenter-link.php', ['block'=>$block]);  ?>
                <?= $this->insert( ConfigurationCache::eventTemplates()['templates']."presenter-job/content/job-list.php", $companyJobs  ); ?>
            </div>
