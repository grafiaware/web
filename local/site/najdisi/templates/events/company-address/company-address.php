<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
if ($editable) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }        
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
                    <input <?= $readonly ?> type="text" name="psc" maxlength="5" 
                                            pattern="[0-9]{5}" title="Zadejte 5 číslic." placeholder="12345"
                                            value="<?= isset($psc)?  $psc : '' ?>">
                </div>
                <div class="field">
                    <label>Obec</label>
                    <input <?= $readonly ?> type="text" name="obec" placeholder="" maxlength="60" value="<?= isset($obec)?  $obec : '' ?>">
                </div>
            </div>                 

            <?php
            if($editable) {
            ?>
                            
            <div>
                <?=
                 isset($companyId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId."/companyaddress/".$companyId."' > Uložit </button>" :
                "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId_proInsert."/companyaddress' > Přidat adresu </button>" ;
                ?>                                                                                                                                             
            </div>

            <?php
            }
            ?>

        </form>           