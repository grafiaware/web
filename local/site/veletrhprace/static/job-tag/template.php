<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Repository\RepresentativeRepo;

use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Repository\JobTagRepo;
use Events\Model\Entity\JobTag;



//       $readonly = 'readonly="1"';
//       $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = ''; 

/** @var PhpTemplateRendererInterface $this */
   
    
//    /** @var CompanyContactRepo $companyContactRepo */
//    $companyContactRepo = $container->get(CompanyContactRepo::class );
//    /** @var RepresentativeRepo $representativeRepo */
//    $representativeRepo = $container->get(RepresentativeRepo::class );
    //------------------------------------------------------------------
       
       

  /** @var JobTagRepoInterface $jobTagRepo */ 
    $jobTagRepo = $container->get(JobTagRepo::class );
    
    $allTags = $jobTagRepo->findAll();
    $allTagsStringRepeat=[];
    /** @var  JobTag $tag */
    foreach ($allTags as $tag) {       
        $allTagsStringRepeat[] = [
            'id'  => $tag->getId(),
            'tag' => $tag->getTag() ];
    } 
  ?>


    <div>
    <div class="ui styled fluid accordion">           
        <div>                
           <b>Možné typy nabízených pozic </b>
        </div>                   
        <hr/>
         
        <div>      
                <?= $this->repeat(__DIR__.'/job-tag.php',  $allTagsStringRepeat)  ?>
        </div>        
   
        <div>  
            <br/>
            Přidej další typ
            <div>     
                <?= $this->insert( __DIR__.'/job-tag.php', [ ] ) ?>                                                                                 
            </div>                  
        </div>          
                              
    </div>
    </div>

