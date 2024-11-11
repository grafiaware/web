<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Entity\CompanyInterface;

use Access\Enum\RoleEnum;

/** @var PhpTemplateRendererInterface $this */

/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$editable = $statusViewModel->getUserRole()===RoleEnum::EVENTS_ADMINISTRATOR;

/** @var CompanyRepo $companyRepo */ 
$companyRepo = $container->get(CompanyRepo::class );

$companies=[];     
foreach ($companyRepo->findAll() as $company) {
    /** @var CompanyInterface $company */
    $companies[] = [
        'editable' => $editable,
        'companyId' => $company->getId(),
        'name' =>  $company->getName()
        ];
}

?>

    <div class="ui styled fluid accordion">   

            <div class="active title">
                <i class="dropdown icon"></i>
                Firmy (companies)
            </div>     
        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company.php',  $companies)  ?>
                <?php
                if($editable) {
                ?>
                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej firmu
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/company.php', ['editable' => $editable]) ?>                                                                                 
                </div> 
                <?php
                }
                ?>                
            </div>            
    </div>

  