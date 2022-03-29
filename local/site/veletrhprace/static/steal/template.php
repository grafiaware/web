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
        $steal = $thief->steal($urlPrefix, $urlPostfix);

        $iframe =         '<head>
                                    <link rel="stylesheet" type="text/css" href="'.$urlPrefix.'public/web/css/lay_bac_grafia.css" />
                                </head>
                                <body>'.$steal.'
                                </body>
        ';
?>
<div class="ui segment">
    <div class="paper">
        <section>
            <div class="">
                <div class="ui grid">
                    <div class="row">
                        <div class="sixteen wide column">
                            <iframe  src="about:blank" frameborder="0" id="steal" width=100% frameborder="0" marginheight="0" marginwidth="0">
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