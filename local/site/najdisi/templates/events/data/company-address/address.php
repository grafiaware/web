<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
 
?>

            <div class="two fields ">                        
                <div class="field">
                    <p class="text primarni-barva zadne-okraje"><?= $name ?? '' ?></p>
                    <p class="text primarni-barva zadne-okraje"><i class="map outline icon"></i>Místo firmy (pro adresu)</p>
                 </div>  
                <div class="field">
                    <p class="text zadne-okraje"><i class="map marker icon"></i><?= $lokace ?? ''  ?></p>
                    <p class="text zadne-okraje"><?= $psc ?? '' ?> <?= $obec ?? '' ?></p>
                </div>
            </div>             
