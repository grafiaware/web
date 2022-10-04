<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

//z presenter-jobs vzniklo mi

?>
            <div id="idecko">
                <p class="velky text primarni-barva tucne"> code: <?= $code ?>   | locale: <?= $locale ?> </p>

                <?php
                if (isset($lanCode)) {
                    ?>
                    <div class="text okraje-vertical">
                        verti
                    </div>
                    <?php
                }
                ?>


                <?php /*$this->insert( ConfigurationCache::componentController()['templates'].
                                      "paper/presenter-job/content/vypis-pozic.php", $presenterJobs) ; */ 
                  ?>
            </div>
