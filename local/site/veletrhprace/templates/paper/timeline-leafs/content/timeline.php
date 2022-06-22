<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */
?>

                <div class="timeline-1">
                    <div class="ui grid">
                        <?= $this->repeat(__DIR__.'/timeline/timeline-point.php', $event) ?>
                    </div>
                </div>