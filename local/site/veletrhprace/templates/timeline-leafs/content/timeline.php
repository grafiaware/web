<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

                <div class="timeline-1">
                    <div class="ui grid">
                        <?= $this->repeat(__DIR__.'/timeline/timeline-point.php', $event) ?>
                    </div>
                </div>