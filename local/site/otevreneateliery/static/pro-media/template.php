<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

include 'data.php';
?>

<div class="paper">
    <headline>
         <?php include "headline.php" ?>
    </headline>
    <perex>
        <?php include "perex.php" ?>
    </perex>
    <content>
        <?= $this->insert(ConfigurationCache::componentController()['templates']."zprava"."/template.php", $tiskovaZprava) ?>
    </content>
</div>
