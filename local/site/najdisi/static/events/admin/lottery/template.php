<?php
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyRepoInterface;

use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;
use Access\Enum\RoleEnum;

/** @var PhpTemplateRendererInterface $this */


/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$representativeActions = $statusViewModel->getRepresentativeActions();
$getEditable = isset($representativeActions) ? $representativeActions->getDataEditable() : false;   //TODO: SV editace dat pro events admin
$userRole = $statusViewModel->getUserRole();
if ($userRole == RoleEnum::EVENTS_ADMINISTRATOR) {

/** @var PhpTemplateRendererInterface $this */
?>       
            <div>
                <?=                
                "<button class='ui primary button' type='submit' "
                                . "formaction='".Text::encodeUrlPath("events/v1/lottery")."' > Administrace losování </button>" 
                ?>     
            </div>      
<?php
    
    
    
} else {
    echo Html::p("Stránka je určena pouze pro administraci.", ["class"=>"ui orange segment"]);
    
}
