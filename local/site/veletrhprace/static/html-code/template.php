<?php

// template
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;
use View\Thief;

        $urlPostfix = "index.php?lay=";
        $urlPrefix = "http://praci.najdisi.cz/";
        $thief = new Thief();
        $steal = $thief->steal($urlPrefix, $urlPostfix);
        $escapedSteal = str_replace("<", "&lt", str_replace("&", "&amp", $steal));

?>
<div class="ui segment">
    <div class="paper">
        <section>
            <div class="">
                <div class="ui grid">
                    <div class="row">
                        <div class="sixteen wide column">
                            <pre style="
  font-family: Consolas, Menlo, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, serif;
  color: yellow;
  font-size: 0.5em;
  line-height: 100%;
  overflow: auto;
  width: auto;
  padding: 5px;
  background-color: #666;
  width: 100%;
  padding-bottom: 10px;
  max-height: 600px;
  "><?= $escapedSteal ?></pre>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>