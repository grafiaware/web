<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

<div class="blok-obr-text">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <img src="@siteimages/veletrh_fyzicky_foto1.jpg" width="1280" height="505" alt="Obrázek"/>
            <p class="text tucne vpravo">
                <?= Text::mono('VELETRH PRÁCE A VZDĚLÁVÁNÍ – Klíč k příležitostem<sup>&reg;</sup> <br/>je určen všem, kdo má zájem získat či změnit zaměstnání. Na své si příjdou i zájemci o brigády 
                    a stáže. Veletrh je také otevřen všem studentům i absolventům středních škol, odborných
                    škol, učilišť a univerzit.') ?>
            </p>
        </div>
    </div> 
</div>