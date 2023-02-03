<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

?>
            <div>
                <?php
                if (isset($block)) {
                    ?>
                    <div class="text okraje-vertical">
                        <a class="ui large button grey" href="<?=  /*"web/v1/page/block/".$block->getName().*/  "#chci-navazat-kontakt" ?>">
                                Chci jít na stánek pro kontaktní údaje
                        </a>
                    </div>
                    <?php
                }
                ?>
            </div>

