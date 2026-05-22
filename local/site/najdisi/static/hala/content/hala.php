<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */


?>

    <div class="online-stanky">
        <div class="ui stackable centered grid">
            <?=$this->insert(__DIR__.'/hala/video-firem.php', $promoVideo) ?>
            <?=$this->repeat(__DIR__.'/hala/rozvrzeni-vystavovatelu.php', $exhibitor) ?>
        </div>
    </div>

