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

    <form class="ui huge form" action="" method="POST" >            
        
        <div class="active title">
                <i class="dropdown icon"></i>
                <?= "Obsah události pro " . ($institutionName ?? '') . " - " . ($title ?? '') ?>
        </div>                        
        <div class="active content">                                                                         
            <div class="eight wide field">  
              <!--  < ?php if (isset($idContent)) { ?>
                    <label>Název instituce</label>
                    <input readonly  type="text" name="institutionName" placeholder="" maxlength="100" value="< ?= isset($institutionName)?  $institutionName: '' ?>">
                < ?php } else { ?>
                     <input hidden type="text" name="selectInstitution" placeholder="" maxlength="11" value="< ?= $institutionIdFk  ?? '' ?>">  
                < ?php } ?>  -->

              
                <?= Html::select( "selectInstitution", " Název instituce:",    $selectInstitutions ??  [] , 
                                    [ "selectInstitution" =>  $institutionIdFk  ?? ''  ] , [ ] ) ?>   
                
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
                <?= Html::select( "selectContentType", "Typ obsahu:", $selectContentTypes ?? []  , 
                                 ["selectContentType" =>  $eventContentTypeFk ?? ''  ] , [ 'required' => true ] ) ?>  
            </div>

            <div>                                                                                                                                
                <?=       
                isset($idContent) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/eventcontent/" . $idContent . "'> Uložit </button>
                    <button class='ui primary button' type='submit' formaction='events/v1/eventcontent/" . $idContent . "/remove'> Odstranit  </button>" 
                    :                
                    "<button class='ui primary button' type='submit' formaction='events/v1/eventcontent' > Uložit </button>" ;                
                ?>                                                                                                         
            </div>
        
        </div>      

    </form >