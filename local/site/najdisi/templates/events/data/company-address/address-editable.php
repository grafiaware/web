<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;
 
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        

?>

            <div class="two fields ">                        
                <div class="field">
                <label>Jméno firmy (pro adresu)</label>
                    <input type="text" name="name" placeholder="" maxlength="100" minlength="1" required value="<?= $name ?? '' ?>">
                    <span></span>
                 </div>  
                <div class="field">
                    <label>Místo firmy (pro adresu)</label>
                    <input type="text" name="lokace" placeholder="" maxlength="100"  required value="<?= $lokace ?? ''  ?>">
                    <span></span>
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>PSČ</label>
                    <input type="text" name="psc" maxlength="5" 
                                            pattern="\d{5}" title="Zadejte 5 číslic." placeholder="12345"
                                            value="<?= $psc ?? '' ?>">
                    <span></span>
                </div>
                <div class="field">
                    <label>Obec</label>
                    <input type="text" name="obec" placeholder="" maxlength="60" value="<?= $obec ?? '' ?>">
                    <span></span>
                </div>
            </div>                 