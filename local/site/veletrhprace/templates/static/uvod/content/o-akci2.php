<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<div class="blok-obr-text">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <img src="images/veletrh_fyzicky_foto1.jpg" width="" height="" alt="Obrázek"/>
            <p class="text tucne vpravo">
                <?= Text::mono('VELETRH PRÁCE A VZDĚLÁVÁNÍ – Klíč k příležitostem<sup>&reg;</sup> <br/>je určen všem, kdo má zájem získat či změnit zaměstnání. Na své si příjdou i zájemci o brigády 
                    a stáže. Veletrh je také otevřen všem studentům i absolventům středních škol, odborných
                    škol, učilišť a univerzit.') ?>
            </p>
        </div>
    </div>
</div>