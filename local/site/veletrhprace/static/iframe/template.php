<?php

// template
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */

use Site\Configuration;
use View\Thief;

        $urlPostfix = "index.php?lay=";
        $urlPrefix = "http://praci.najdisi.cz/";
        $thief = new Thief();
        $perex = $thief->steal($urlPrefix, $urlPostfix);
?>
<div class="ui segment">
    <div class="paper editable">
        <section>
                <headline class="ui header borderDance">
                    <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll"><?= $headline ?? '' ?></p>
                </headline>
        </section>
        <section>
                <perex class="borderDance">
            <?= $perex ?? ''?>
                </perex>
        </section>
        <section>
            <div class="">
                <div class="ui grid">
                    <div class="row">
                        <div class="sixteen wide column">
                            <iframe src="<?= $src ?>" frameborder="0" id="gformQQQ" width=100% height="4000" frameborder="0" marginheight="0" marginwidth="0">Načítání…</iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>