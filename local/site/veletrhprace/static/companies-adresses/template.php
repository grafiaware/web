<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

?>

            <div class="active title">
                <i class="dropdown icon"></i>
                Adresy vystavovatele
            </div>                        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/content/company/company-a...php', $companyAdresses  )  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej další kontakt vystavovatele
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/content/company/company-a.....php', [ 'companyId' => $idCompanyFromRepresentative ] ) ?>                                                                                 
                </div>                  
            </div>            


