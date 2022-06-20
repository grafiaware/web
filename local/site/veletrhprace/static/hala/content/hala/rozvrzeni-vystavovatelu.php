<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */


?>


        <div class="three column stretched row">
            <?=
            $this->repeat(__DIR__.'/rozvrzeni-vystavovatelu/vystavovatel.php', $row);
        ?>
        </div>

