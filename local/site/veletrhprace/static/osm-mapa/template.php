<?php
// template
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

//https://developer.mozilla.org/en-US/docs/Web/HTML/Element/iframe

    $iframeId = 'inlineFrameExample';
    $iframe = '
        title="Inline Frame Example"
        width="300"
        height="200"
        src="https://www.openstreetmap.org/export/embed.html?bbox=-0.004017949104309083%2C51.47612752641776%2C0.00030577182769775396%2C51.478569861898606&layer=mapnik">
        ';


        ?>
<div class="ui segment">
    <div class="paper">
        <section>
            <div class="">
                <div class="ui grid">
                    <div class="row">
                        <div class="sixteen wide column">
                            <iframe id="<?=$iframeId?>"  src="about:blank" frameborder="0" width=100% frameborder="0" marginheight="0" marginwidth="0">
<?= $iframe ?>
                            </iframe>
<script>
document.querySelector('iframe').contentDocument.write("<?= $iframe ?>");
</script>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>