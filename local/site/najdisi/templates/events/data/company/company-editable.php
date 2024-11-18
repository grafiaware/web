<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
?>

        <form class="ui huge form" action="" method="POST" >

            <div class="two fields">                        
                <div class="field">
                <label>Název firmy</label>
                    <input type="text" name="name" placeholder="" maxlength="250" value="<?= $name ?? '' ?>" required >
                 </div>  
            </div>                
                <div>
                    <?=
                     isset($companyId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId . "' > Uložit změny </button>" :
                    "<button class='ui primary button' type='submit' formaction='events/v1/company' > Uložit </button>" ;                                   
                     ?>                                                                                                                             
                    <?=
                    isset($companyId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId."/remove' > Odstranit firmu </button>" :
                    "" ;
                    ?>                                                                                                         
                </div>   
        </form>           