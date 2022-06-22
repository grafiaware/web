<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */
?>

                <div class="prednasejici">
                    <p class="text velky primarni-barva nastred"><?= $timelinePoint ?></p>
                    <div class="ui two column internally celled grid centered">
                        <div class="stretched row">
                            <div class="eight wide column"><p><b>Název poradny</b></p><p>Čas</p></div>
                            <div class="eight wide column"><p><b>Poradí vám</b></p><p>Téma</p></div>
                        </div>
                        <?= $this->repeat(__DIR__.'/timerow/stretched-row.php', $box) ?>
                    </div>
                </div>