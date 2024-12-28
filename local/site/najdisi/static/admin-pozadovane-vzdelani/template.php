<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;
use Access\Enum\RoleEnum;

use Events\Model\Repository\PozadovaneVzdelaniRepoInterface;
use Events\Model\Repository\PozadovaneVzdelaniRepo;
use Events\Model\Entity\PozadovaneVzdelaniInterface;

/** @var PhpTemplateRendererInterface $this */
/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$userRole = $statusViewModel->getUserRole();
if ( $userRole == RoleEnum::EVENTS_ADMINISTRATOR ) {
    
    /** @var PozadovaneVzdelaniRepoInterface $pozadovaneVzdelaniRepo */ 
    $pozadovaneVzdelaniRepo = $container->get(PozadovaneVzdelaniRepo::class );
    //------------------------------------------------------------------
 
    $allVzdelani = $pozadovaneVzdelaniRepo->findAll();
    $allVzdelaniArray=[];
    //$allVzdelaniString=[]; 
    /** @var  PozadovaneVzdelaniInterface $vzdelani */
    foreach ($allVzdelani as $vzdelani) {    
        $vzd ['stupen'] = $vzdelani->getStupen();
        $vzd ['vzdelani'] = $vzdelani->getVzdelani();   
        $allVzdelaniArray[] = $vzd;  
        //$tmpArray [  $vzdelani->getStupen()] = $vzd;            
    }
//    //seradit
//    ksort( $tmpArray );
//    foreach ($tmpArray as $vzdel2) {  
//        $allVzdelaniArray[] = $vzdel2; 
//    }
    
   
    //$pro_min =  $vzdelani->getStupen() +1 ;       
  ?>

    
    <div class="ui styled fluid accordion">   
        
        <div>                
           <b>Stupeň vzdělání</b>
        </div>                   
        <div>
           <!--  < ?= /* $this->repeat(__DIR__.'/job-tagSeznam.php', $allTagsString, 'seznam')  */ ? > -->
        </div>
        ------------------------------------------------------        
        
         <div>      
            <?= $this->repeat(__DIR__.'/pozadovane-vzdelani.php',  $allVzdelaniArray)  ?>
            <div>                   
                Přidej další vzdelani
            </div>  
            <div>     
                <?= $this->insert( __DIR__.'/pozadovane-vzdelani.php' /*, [ 'pro_minimum'=>$pro_min ] */ ) ?>
            </div>                  
        </div>           
                                      
    </div>
<?php
} else {
    echo "stránka je určena pouze pro administraci.";
}
?>
