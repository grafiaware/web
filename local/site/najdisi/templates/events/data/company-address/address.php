<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
 
?>
            <div class="ui grid">
                <div class="sixteen wide column">
                    <div class="ui segment">    
                        <p class="text primarni-barva zadne-okraje"><i class="building outline icon"></i><?= $name ?? '' ?></p>
                        <p class="text zadne-okraje"><i class="map outline icon"></i><?= $lokace ?? ''  ?></p>
                        <p class="text zadne-okraje"><i class="map marker icon"></i><?= $psc ?? '' ?> <?= $obec ?? '' ?></p>
                    </div>
                </div>
            </div>
