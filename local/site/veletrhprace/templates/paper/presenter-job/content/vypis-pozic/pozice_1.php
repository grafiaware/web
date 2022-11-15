<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

use Site\ConfigurationCache;
use Status\Model\Repository\StatusSecurityRepo;
use Auth\Model\Entity\LoginAggregateFullInterface;

use Events\Middleware\Events\Controler\VisitorControler;
use Events\Model\Repository\VisitorProfileRepo;
use Events\Model\Entity\VisitorProfileInterface;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Entity\VisitorJobRequestInterface;

use Events\Model\Arraymodel\Presenter;


/** @var PhpTemplateRendererInterface $this */
?>
AAAAAAAAAAAAAAAAAA  pozice_1 AAAAAAAAAAAAAAAA
        <div class="title">
            <p class="podnadpis"><i class="dropdown icon"></i><?= $nazev ?>, <?= $mistoVykonu ?>
              
          

                
            </p>
        </div>
        <div class="content">
            <div class="ui grid stackable">
                <div class="row">
                    <div class="four wide column"><b>Místo výkonu práce:</b></div>
                    <div class="six wide column"><?= $mistoVykonu ?></div>
                </div>
                <div class="row">
                    <div class="four wide column"><b>Požadované vzdělání:</b></div>
                    <div class="six wide column"><?= $vzdelani ?></div>
                    
                    
                    
                </div>
                <div class="row">
                    <div class="four wide column">
                        <p><b>Popis pracovní pozice:</b></p>
                    </div>
                    <div class="twelve wide column"><div><?= $popisPozice ?></div></div>
                </div>
                <div class="row">
                    <div class="eight wide column">
                        <p><b>Požadujeme:</b></p>
                        <div>
                            <?= $this->repeat(__DIR__.'/pozice/li.php', $pozadujeme, 'seznam') ?>
                        </div>
                    </div>
                    <div class="eight wide column">
                        <p><b>Nabízíme:</b></p>
                        <div>
                            <?= $this->repeat(__DIR__.'/pozice/li.php', $nabizime, 'seznam') ?>
                        </div>
                    </div>
                </div>
                
                                                      
                
                
                
                
            </div>
        </div>