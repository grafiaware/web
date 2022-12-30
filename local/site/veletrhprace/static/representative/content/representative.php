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
            <input type='hidden' name="company-idH" value="<?= isset($companyId)? $companyId : '' ?>" >
            <input type='hidden' name="login-login-nameH" value="<?= isset($loginLoginName)? $loginLoginName : '' ?>" >

            <div class="three fields">                        
                <div class="field">
                <label>CompanyId</label>
                    <input <?= $readonly ?> type="text" name="company-id" placeholder="" maxlength="10" value="<?= isset($companyId)?  $companyId : '' ?>">
                </div> 
                
                <div class="field">
                    <?php if (isset($companyName) ){ ?>
                            <label>CompanyName</label>
                            <input <?= $readonly ?> type="text" name="company-name" placeholder="" maxlength="250" value="<?= isset($companyName)?  $companyName : '' ?>">
                    <?php } else { ?>
                            <?= Html::select("selectCompany", "Company name:", $selectCompanies, [], []) ?>                      
                    <?php } ?>    
                </div>
                
                <div class="field"> 
                    <?php  if (isset($loginLoginName) ) { ?>
                            <label>loginLoginName</label>
                            <input <?= $readonly ?> type="text" name="login-login-name" placeholder="" maxlength="50" value="<?= isset($loginLoginName)?  $loginLoginName : ''  ?>">
                    <?php } else { ?>
                            <?= Html::select("selectLogin", "Login name:",  $selectLogins, [], []) ?>   
                    <?php } ?> 
                </div>
            </div>
                    

            <?php
            if($readonly === '') {
            ?>
            <div>
                <?=
                 isset($companyId) ? "" :
                "<button class='ui primary button' type='submit' formaction='events/v1/representative' > Uložit </button>" ;
                
//                 isset($companyId) ?
//                "<button class='ui primary button' type='submit' formaction='events/v1/representative/". $companyId. "/" . $loginLoginName ."' > Uložit </button>" :
//                "<button class='ui primary button' type='submit' formaction='events/v1/representative' > Uložit </button>" ;
                ?>                                                                                                                             
                <?=
                 isset($companyId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/representative/". $companyId. "/" . $loginLoginName  ."/remove' > Odstranit representative </button>" :
                "" ;
                ?>                                                                                                         
            </div>
            <?php
            }
            ?>

        </form>           
