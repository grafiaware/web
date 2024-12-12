<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
 
?>

            <div class="two fields ">                        
                <div class="field">
                <label>Jméno firmy (pro adresu)</label>
                <p><?= $name ?? '' ?></p>
                 </div>  
                <div class="field">
                    <label>Lokace</label>
                    <p><?= $lokace ?? ''  ?></p>
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>PSČ</label>
                    <p><?= $psc ?? '' ?></p>
                </div>
                <div class="field">
                    <label>Obec</label>
                    <p><?= $obec ?? '' ?></p>
                </div>
            </div>                 
