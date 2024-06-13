<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>



            <div class="title">
                <i class="dropdown icon"></i>
                Igelitka
            </div>
            <div class="content">
                <div class="ui three column grid centered">
                    <?= $this->repeat(__DIR__.'/igelitka/letak.php', $letak) ?>
                </div>
            </div>