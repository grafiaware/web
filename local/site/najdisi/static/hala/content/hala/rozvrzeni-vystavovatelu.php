<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */


?>


        <div class="three column stretched row">
            <?=
            $this->repeat(__DIR__.'/rozvrzeni-vystavovatelu/vystavovatel.php', $row);
        ?>
        </div>

