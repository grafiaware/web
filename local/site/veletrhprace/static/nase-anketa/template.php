<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */

//https://forms.gle/w5NTnXbxEg6GGRLp7

$formUid = "1FAIpQLSdupUpxH5KBKVaQoZzLWlzeX0jdp2OX25aRc5ar53CEfZTzcg";
?>
<div class="ui segment">
    <div class="paper editable">
        <section>
            <form>
                <headline class="ui header borderDance">
                    <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Anketa a slosování</p>
                </headline>
            </form>
        </section>
        <section>
            <form>
                <perex class="borderDance">
            <?= Text::mono('
            <p class="text">Vyplňte ANKETU NÁVŠTĚVNÍKA</a> a zařaďte se do slosování o ceny, které proběhne 28. dubna!</p>
            <p class="text okraje-vertical"></p>
            ')?>
                </perex>
            </form>
        </section>
        <section>
            <div class="">
                <div class="ui grid">
                    <div class="row">
                        <div class="sixteen wide column">
                            <iframe src="https://docs.google.com/forms/d/e/<?= $formUid ?>/viewform?embedded=true" width="650" height="1000" frameborder="0" marginheight="0" marginwidth="0">Načítání…</iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>