<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
 

    //    $readonly = 'readonly="1"';
    //    $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = ''; 

?>
   <!--                'institutionIdFk' => $entity->getId(),
                    'institutionName' => $institutionE->getName(),
                    'eventContentTypeFk' => $entity->getCompanyId(),                    
                    'title' =>  $entity->getTitle(),
                    'perex' =>  $entity->getPerex(),
                    'party' =>  $entity->getParty(),
                    'idContent' =>  $entity->getId() 
    $selecty['selectInstitution'] = $selectInstitution;
    $selecty['selectContentType'] = $selectContentType;

-->

    <form class="ui huge form" action="" method="POST" >                       
      
        <!-- < ?php  if (isset ($idContent) ) {   ?>        -->                         
            <div>       
                <div class="eight wide field">  
                    <?= Html::select( "institutionIdFk", " Název instituce:",  $selectInstitution ?? [] , 
                                     ["institutionIdFk" => $institutionIdFk ?? '' ] , [] ) ?>  
                </div>
                
                Title:
                <div  class="field">
                    <input <?= $readonly ?> type="text" name="title"  maxlength="200" value="<?= isset($title) ? $title : ''?>" >
                </div>
                Perex:   
                <div class="field">
                    <input <?= $readonly ?> type="text" name="perex" placeholder="" maxlength="500" value="<?= isset($perex) ? $perex : '' ?>" >
                </div>
                Party:   
                <div  class="field">
                    <input <?= $readonly ?> type="text" name="party" placeholder="" maxlength="200" value="<?= isset($party) ? $party : '' ?>" >
                </div>
              
                <div class="eight wide field">                                  
                    <?= Html::select( "eventContentTypeFk", "Typ obsahu:", $selectContentType ?? [] , 
                                     ["eventContentTypeFk" =>  $eventContentTypeFk ?? '' ] , [] ) ?>  
                </div>

            </div>  
        <!--  < ?php  }  else { ?>   -->
        
        
        
            
           
    
        <div>                                                                                                                                
            <?=
       
            isset($idContent) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/contenttype/" . $idContent . "'> Uložit </button>
                <button class='ui primary button' type='submit' formaction='events/v1/contenttype/" . $idContent . "/remove'> Odstranit  </button>" 
                :                
                "<button class='ui primary button' type='submit' formaction='events/v1/contenttype' > Uložit </button>" ;                
            ?>                                                                                                         
        </div>

    </form >