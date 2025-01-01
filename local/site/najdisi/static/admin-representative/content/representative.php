<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

    //    $readonly = 'readonly="1"';
    //    $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = ''; 
?>

        <form class="ui huge form" action="" method="POST" >      

            <div class="three fields">                        
                <!-- <div class="field">
                <label>CompanyId</label>
                    <input < ?= $readonly ?> type="text" name="company-id" placeholder="" maxlength="10" value="< ?= isset($companyId)?  $companyId : '' ?>">
                </div>  -->
                
                <div class="field">
                    <?php if (isset($companyName) ){ ?>
                            <label>Firma - CompanyName</label>
                            <input <?= $readonly ?> type="text" name="company-name" placeholder="" maxlength="250" value="<?= $companyName ?>">
                    <?php } else { ?>
                            <?= Html::select("selectCompany", "Firma - Company name:",
                                            [ ],
                                            $selectCompanies, 
                                            ['required' => true ],
                                            ''   ) ?>                      
                    <?php } ?>    
                </div>
                
                <div class="field"> 
                    <?php  if (isset($loginLoginName) ) { ?>
                            <label>Representant - loginLoginName</label>
                            <input <?= $readonly ?> type="text" name="login-login-name" placeholder="" maxlength="50" value="<?=  $loginLoginName  ?>">
                    <?php } else { ?>
                            <?= Html::select("selectLogin", "Representant - Login name:",  
                                             [ ],                                    
                                             $selectLogins,
                                             ['required' => true ],                                  
                                            '') ?>   
                    <?php } ?> 
                </div>
            </div>
                    

            <?php
            if($readonly === '') {
            ?>
            <div>
                <?=
                 isset($companyId) ?
                "<button class='ui primary button' type='submit' formaction='".rawurlencode("events/v1/representative/$loginLoginName/$companyId/remove")."' > Odstranit representanta </button>" :    
                "<button class='ui primary button' type='submit' formaction='".rawurlencode("events/v1/representative")."' > Uložit </button>" ;                
                ?>                                                                                                                                                                                                                                                 
            </div>
            <?php
            }
            ?>

        </form>           
