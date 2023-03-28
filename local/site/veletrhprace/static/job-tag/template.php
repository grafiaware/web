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

/** @var PhpTemplateRendererInterface $this */

    /** @var JobTagRepoInterface $jobTagRepo */ 
    $jobTagRepo = $container->get(JobTagRepo::class );
    
//    /** @var CompanyContactRepo $companyContactRepo */
//    $companyContactRepo = $container->get(CompanyContactRepo::class );
//    /** @var RepresentativeRepo $representativeRepo */
//    $representativeRepo = $container->get(RepresentativeRepo::class );
    //------------------------------------------------------------------

 
    $allTags = $jobTagRepo->findAll();
    $allTagsString=[];
    /** @var  JobTag $tag */
    foreach ($allTags as $tag) {
        $allTagsString[] = $tag->getTag();
        
        $allTagsStringRepeat[] = ['tag' => $tag->getTag()];
    }
    
    
    
    
    
    
//    if (isset ($companyEntity)) {       
//            
//        $companyContacts=[];
//        $companyContactEntities = $companyContactRepo->find( " company_id = :idCompany ",  ['idCompany'=> $idCompany ] );
//        if ($companyContactEntities) {         
//            foreach ($companyContactEntities as $cCEntity) {
//                /** @var CompanyContactInterface $cCEntity */
//                $companyContacts[] = [
//                    'companyContactId' => $cCEntity->getId(),
//                    'companyId' => $cCEntity->getCompanyId(),
//                    'name' =>  $cCEntity->getName(),
//                    'phones' =>  $cCEntity->getPhones(),
//                    'mobiles' =>  $cCEntity->getMobiles(),
//                    'emails' =>  $cCEntity->getEmails()
//                    ];
//            }   
//        }             
//        
  ?>


    <div>
    <div class="ui styled fluid accordion">   
        
        <div>                
           <b>Typy nabízených pozic </b>
        </div>   
                
        <div>
            <?= $this->repeat(__DIR__.'/job-tagSeznam.php', $allTagsString, 'seznam') ?>
        </div>
        
        
        
         <div>      
                <?= $this->repeat(__DIR__.'/job-tag.php',  $allTagsStringRepeat)  ?>
                <div>                   
                    Přidej další typ
                </div>  
                <div>     
                    <?= $this->insert( __DIR__.'/job-tag.php', [] ) ?>                                                                                 
                </div>                  
        </div>           
        
        
        
        
        
        
                 
    </div>
    </div>

