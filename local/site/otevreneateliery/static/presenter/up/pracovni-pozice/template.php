<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */


$headline = 'Pracovní pozice';
?>

<div id="pracovni-pozice">
    <article class="paper">
        <section>
            <headline>
                <p class="nadpis nastred podtrzeny show-on-scroll nadpis-scroll"><?= Text::mono($headline) ?></p>
            </headline>
        </section>
        <section>    
            <content>
                <p>Pro vyhledání všech pracovních pozic klikněte <a href="https://www.uradprace.cz/volna-mista-v-cr" target="_blank">sem</a></p>
            </content>
        </section>
    </article>
</div>