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
    <br>
    
<!--                'institutionIdFk' => $entity->getId(),
                    'institutionName' => $institutionE->getName(),
                    'eventContentTypeFk' => $entity->getCompanyId(),
                    
                    'title' =>  $entity->getTitle(),
                    'perex' =>  $entity->getPerex(),
                    'party' =>  $entity->getParty(),
                    'idI' =>  $entity->getId() -->

    <form class="ui huge form" action="" method="POST" >                       
      
            <div class="fields">  
            <?php  if (isset ($idI) ) {   ?>   
                
                    Název instituce: --select!
                    <div class="field">    
                    <input <?= $readonly ?> type="text" name="institutionIdFk???" placeholder="" maxlength="200" 
                                            value="<?= isset($institutionIdFk) ? $institutionIdFk : '' ?>">
                    
                    </div>
                    Title:
                    <div class="field">
                    <input <?= $readonly ?> type="text" name="title"  maxlength="200" value="<?= isset($title) ? $title : ''?>" >
                    </div>
                    Perex:   
                    <div class="field">
                    <input <?= $readonly ?> type="text" name="perex" placeholder="" maxlength="500" value="<?= isset($perex) ? $perex : '' ?>">
                    </div>
                    Party:   
                    <div class="field">
                    <input <?= $readonly ?> type="text" name="party" placeholder="" maxlength="200" value="<?= isset($party) ? $party : '' ?>">
                    </div>
                    Typ obsahu: --select!
                    <div class="field">    
                    <input <?= $readonly ?> type="text" name="event_content_type_fk" placeholder="" maxlength="45" 
                                            value="<?= isset($eventContentTypeFk) ? $eventContentTypeFk : '' ?>" >
 
                    
                    
            <?php  } else {   ?>    
                    <p>Typ:</p>
                    <div class="field">
                    <input <?= $readonly ?>  type="text" name="type"  maxlength="45"  value="" >
                    </div>
                    Name:  
                    <div class="field">
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="45" value="" >
                    </div>
            <?php  } ?>    
            </div>
    
        <div>                                                                                                                                
            <?=
            isset($type) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/contenttype/" . $type . "'> Uložit </button>" .
                "<button class='ui primary button' type='submit' formaction='events/v1/contenttype/" . $type . "/remove'> Odstranit  </button>" :
                
                "<button class='ui primary button' type='submit' formaction='events/v1/contenttype' > Uložit </button>" ;                
            ?>                                                                                                         
        </div>

    </form >