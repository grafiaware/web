<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

?>   

        <tr>
            <td>                         
                <?= $loginNameFk  ?>                   
            </td>       
            <td>                                                       
                <div  class="field">
                    <input form="<?="credentials_$loginNameFk"?>" readonly type="text" name="passwordHash"  maxlength="255" 
                                            value="<?=$passwordHash?>" >
                </div>
            </td>  
            <td>                        
                <?= Html::select( 
                        "selectRole", 
                        "",  
                        $selected,  
                        $selectRoles,
                        ["form"=>"credentials_$loginNameFk", "onchange"=>"removeDisabled('credentials_button_$loginNameFk')"]
                        ) ?>   
            </td>                
            <td>  
                <div>      
                <button disabled id="<?="credentials_button_$loginNameFk"?>" form="<?="credentials_$loginNameFk"?>" class='ui secondary button' 
                        type='submit' formaction='<?= rawurlencode("auth/v1/credentials/$loginNameFk") ?>'> Uložit </button>
                </div>           
            </td>                   
        </tr>   
        
