<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Repository\JobTagRepo;
use Events\Model\Entity\JobTagInterface;

/** @var PhpTemplateRendererInterface $this */

    /** @var JobTagRepoInterface $jobTagRepo */ 
    $jobTagRepo = $container->get(JobTagRepo::class );
    //------------------------------------------------------------------
 
    $allTags = $jobTagRepo->findAll();
    $allTagsArray=[];
    $allTagsString=[]; 
    /** @var  JobTagInterface $tag */
    foreach ($allTags as $tag) {        
        $tg ['tag'] = $tag->getTag();
        $tg ['tagId'] = $tag->getId();               
        $allTagsArray[] = $tg;       
        $allTagsString[] = $tag->getTag();
    }
             
  ?>

    
    <div class="ui styled fluid accordion">   

        <div>                
           <b>Typy nabízených pozic </b>
        </div>                   
        <div>
            <?= $this->repeat(__DIR__.'/job-tagSeznam.php', $allTagsString, 'seznam') ?>
        </div>
        ------------------------------------------------------        
        
         <div>      
            <?= $this->repeat(__DIR__.'/job-tag.php',  $allTagsArray)  ?>
            <div>                   
                Přidej další typ
            </div>  
            <div>     
                <?= $this->insert( __DIR__.'/job-tag.php' ) ?>                                                                                 
            </div>                  
        </div>           
                                      
    </div>

