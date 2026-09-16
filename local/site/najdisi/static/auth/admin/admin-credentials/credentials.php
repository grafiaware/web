<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
//use Red\Model\Entity\PaperAggregateInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */
 
?>   
<form class="ui huge form" action="" method="POST" > 
        <table>
        <tr>
            <td>                         
                <?= $loginNameFk  ?>                   
            </td>       
            <td>                                                       
                <div  class="field">
                    <input readonly type="text" name="passwordHash"  maxlength="255" 
                                            value="<?= isset($passwordHash) ? $passwordHash : ''?>" >
                </div>
            </td>  
            <td>                        
                <?= Html::select( "selectRole", "",  
                                  [ "selectRole" =>  $roleFk  ?? ''  ],  
                                  $selectRoles ??  [] , 
                                  [ ] ) ?>   
            </td>                
            <td>  
                <div>      
                <button class='ui secondary button' type='submit' formaction='<?= "auth/v1/credentials/$loginNameFk" ?>'> Uložit </button>
                </div>           
            </td>                   
        </tr>   
        </table>
        
        </form >
