<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
    
?>

    <div class="ui styled fluid accordion">   
       
        <div class="title">
            <i class="dropdown icon"></i> 
                <?= $nazev ?? '' ?> 
            <div class="ui grid stackable">       
                <div class="sixteen wide column">
                    <?php
                    if ($dataRedApiUri) {
                        echo Html::tag('span', 
                            [
                                'class'=>'cascade',
                                'data-red-apiuri'=> $dataRedApiUri,
                            ]
                        );
                    } else {
                        echo "<p>Tagy lze k pozici vybírat po prvním uložení pozice.</p>";
                    }
                    ?>                 
                </div>                
                 <div class="sixteen wide column">
                    <p class="text tucne zadne-okraje">Popis pozice:</p>
                    <p><?= $popisPozice ?? '' ?></p>
                </div>
            </div>            
        </div>
        <div class="content">
            <div class="ui grid stackable">       
                <div class="five wide column">
                    <p><b>Místo výkonu:</b></p>
                </div>
                <div class="ten wide column">
                    <p><?= $mistoVykonu ?? '' ?></p>
                </div>
                <div class="five wide column">
                    <p><b>Požadované vzdělání: </b></p>
                </div>
                <div class="ten wide column">
                    <p><?= $selectEducations [$pozadovaneVzdelaniStupen] ?? '' ?></p>
                </div>               

                <div class="ui grid stackable">
                    <div class="eight wide column">
                        <p class="text tucne zadne-okraje">Požadujeme:</p>
                        <p><?= $pozadujeme ?? '' ?></p>
                    </div>                 
                    <div class="eight wide column">
                        <p class="text tucne zadne-okraje">Nabízíme:</p>
                        <p><?= $nabizime ?? '' ?></p>
                    </div>
                </div>                
            </div>
        </div>   
    </div>        




