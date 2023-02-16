<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Entity\PozadovaneVzdelaniInterface;
use Events\Model\Entity\PozadovaneVzdelani;

use Events\Model\Repository\PozadovaneVzdelaniRepo;
use Events\Model\Repository\PozadovaneVzdelaniRepoInterface;



//       $readonly = 'readonly="1"';
//       $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = ''; 

/** @var PhpTemplateRendererInterface $this */   
       

    /** @var PozadovaneVzdelaniRepo $pozadovaneVzdelaniRepo */ 
    $pozadovaneVzdelaniRepo = $container->get(PozadovaneVzdelaniRepo::class );
    
    $allPozadovaneVzdelani = $pozadovaneVzdelaniRepo->findAll();
    $allPozadovaneVzdelaniA=[];
    /** @var  JobTag $tag */
    foreach ($allPozadovaneVzdelani as $pozVzdel) {       
        $allPozadovaneVzdelaniA[] = [ 'stupen'  => $pozVzdel->getVzdelani(),
                                      'vzdelani'  => $pozVzdel->getVzdelani()
                                    ];
    } 
  ?>


    <div>
    <div class="ui styled fluid accordion">           
        <div>                
           <b>Možné stupně požadovaného vzdělání </b>
        </div>                   
        <hr/>
         
        <div>      
                <?= $this->repeat(__DIR__.'/pozadovane-vzdelani.php',  $allPozadovaneVzdelaniA )  ?>
        </div>        
   
        <div>  
            <br/>
            Přidej další 
            <div>     
                <?= $this->insert( __DIR__.'/pozadovane-vzdelani.php', [ ] ) ?>                                                                                 
            </div>                  
        </div>          
                              
    </div>
    </div>

