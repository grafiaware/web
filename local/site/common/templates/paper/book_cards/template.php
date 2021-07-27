<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Component\Renderer\Html\Authored\Paper\ElementWrapper;
use Component\Renderer\Html\Authored\Paper\Buttons;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ElementWrapper $elementWrapper */
/** @var Buttons $buttons */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

?>

    <article class="" data-template="<?= "book_cards" ?>">
        <section class="">
            <?= $headline ?>
            <?= $perex ?>
        </section>
        <div class="ui grid">
            <div class="sixteen wide column">
                <div class="ui cards centered">
                    <div class="ui card">
                        <content id="" class="">
                            <div class="content">
                                <p><i class="user circular icon"></i> Tomáš Lebenhart</p> 
                            </div>
                            <div class="content center aligned">
                                <img class="card-image" src="contents/images/svlekl.jpg">
                            </div>
                            <div class="content">
                                <h3>Svlékl jsem bílý plášť</h3>
                                <p>Tato kniha je pro odvážné a zvídavé. Pro ty, kteří se nechtějí rozhodovat podle reklamních mágů, nabízejících jednoduchá instantní řešení na cokoli. Je pro ty, kdo se rozhodli vzít kvalitu svého života do svých rukou.</p>
                                <div class="content">
                                    <p class="cena">272 Kč</p> 
                                    <span class="right floated">
                                        <button class="ui button">Koupit</button>
                                    </span>
                                </div>
                            </div> 
                            <div class="extra content">
                                <p> <i class="large barcode icon"></i> ISBN: 978-80-87046-43-2</p>
                            </div>
                        </content>
                    </div>
                    <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/book_cards/template.php", $contents, 'paperContent'); ?>
                </div>
            </div>
        </div>
    </article>