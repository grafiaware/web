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
        
        <tr>
        <td>                         
                    <?= $loginNameFk  ?>                   
        </td>         
        
        <td>    
                                                                                 
                           

              
                <?= Html::select( "selectRoles", "",  
                                  [ "selectRoles" =>  $roleFk  ?? ''  ],  
                                   $selectRoles ??  [] , 
                                   [ ] ) ?>   
            </td>   
                
             
           
         
               <td>  
            <div>      
                 <?= 
               "<button class='ui secondary button' type='submit' formaction='events/v1/credentials/" . $loginNameFk . "'> Uložit </button>"   
                ?>
            </div>
           
             </td>   
                
           </tr>      
        
        
            
                                   
   
    </form >