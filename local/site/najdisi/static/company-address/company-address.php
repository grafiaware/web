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

            <div class="two fields ">                        
                <div class="field">
                <label>Jméno firmy (pro adresu)</label>
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="100" minlength="1" required value="<?= isset($name)?  $name : '' ?>">
                 </div>  
                <div class="field">
                    <label>Lokace</label>
                    <input <?= $readonly ?> type="text" name="lokace" placeholder="" maxlength="100"  required value="<?= isset($lokace)?  $lokace : ''  ?>">
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>PSČ</label>
                    <input <?= $readonly ?> type="text" name="psc" placeholder="" maxlength="5" 
                                            pattern="[0-9]{5}" title="Zadejte 5 číslic." placeholder="123 45"
                                            value="<?= isset($psc)?  $psc : '' ?>">
                </div>
                <div class="field">
                    <label>Obec</label>
                    <input <?= $readonly ?> type="text" name="obec" placeholder="" maxlength="60" value="<?= isset($obec)?  $obec : '' ?>">
                </div>
            </div>                 

            <?php
            if($readonly === '') {
            ?>
                            
            <div>
                <?=
                 isset($companyId_proAdress) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId."/companyaddress/" .$companyId_proAdress. "' > Uložit </button>" :
                "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId."/companyaddress' > Přidat adresu </button>" ;
                ?>                                                                                                                                             
            </div>

            <?php
            }
            ?>

        </form>           