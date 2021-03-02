<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

                <div class="accordion">
                    <div class="title">
                        <i class="dropdown icon"></i>
                        <?= $datum ?>
                    </div>
                    <div class="content">
                        <p>text text text</p>
                        
                        <div class="accordion">
                            <div class="title">
                                <i class="dropdown icon"></i>
                                Přednášky
                            </div>
                            <?= $this->repeat(__DIR__.'/vypis-dne/prednasky.php', $prednasky) ?>
                        </div>
                        <div class="accordion">
                            <div class="title">
                                <i class="dropdown icon"></i>
                                Pohovory
                            </div>
                            <?= $this->repeat(__DIR__.'/vypis-dne/pohovory.php', $pohovory) ?>
                        </div>
                    </div>
                </div>

