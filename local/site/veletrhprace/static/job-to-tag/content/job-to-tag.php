<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Entity\JobTagInterface;
use Events\Model\Repository\JobTagRepo;

use Moje\MojeHTML;



/** @var PhpTemplateRendererInterface $this */

    //    $readonly = 'readonly="1"';
    //    $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = '';                 
        
      //  include "MojeHTML.php";
      //          
//    $selectJobTags2 =[];    
//        //  ############################     TADY NENI CONTAINER   ############################
//    /** @var JobTagRepoInterface $jobTagRepo */ 
//    $jobTagRepo = $container->get(JobTagRepo::class );    
//    $jobTagEntities2 = $jobTagRepo->findAll();
//        /** @var JobTagInterface  $jobTagEntity2 */ 
//    foreach ( $jobTagEntities2 as $jobTagEntity2) {
//        $selectJobTags2 [] =  $jobTagEntity2->getTag() ;
//    }      
        
?>
<?php if ( (isset($jobNazev)/*and ($jobNazev != "") */) ) { ?>
        <form class="ui huge form" action="" method="POST" >      
            <div class="fields">                        
                <!-- <div class="field">
                <label>Job Id</label>
                    <input < ?= $readonly ?> type="text" name="job-id" placeholder="" maxlength="10" value="< ?= isset($jobId)?  $jobId : '' ?>">
                </div>  -->               
                <div class="field">                                                   
                        <label>Název pozice:</label>
                          <input <?= $readonly ?> type="text" name="job-nazev" placeholder="" maxlength="45" value="<?= isset($jobNazev)?  $jobNazev : '' ?>">
                        <label>Typy přiřazené této pozici:</label>                               
                          <?= $this->repeat(__DIR__.'/job-tag.php', $jobTagTags, 'seznam') ?>
                        <br/><br/>  
                        
                        <div class="two fields">
                            <div class="field"> 
                             <?= Html::select("selectJobTagPridej", "Vyber typ pozice a zvol akci Přidat/Odtranit:",  $selectJobTags, [], [] ) ?> 
                            </div>                               
                        </div>
                        
                        <button class='ui primary button' type='submit' formaction='events/v1/jobtotag/<?= $jobId ?>' > Přidat typ </button> 
                        <button class='ui primary button' type='submit' formaction='events/v1/jobtotag/<?= $jobId ?>/remove' > Odstranit typ </button>                                                                                                                            
                </div>          
            </div> 
            <?php 
             $allTags = [];
             foreach ($selectJobTags as $val) {
                $allTags[$val] = [$val => $val] ;
             }
             //$jobTagTags
             $checkTags= [];
             foreach ($jobTagTags as $val) {
                $checkTags[] = [$val => $val] ;
             }
             
            ?>
            <?= Html::checkbox( $allTags ,
                                [ 'manažerská/vedoucí'=>'manažerská/vedoucí', 'technická'=>'technická']); ?> 
            <br/>
            <?= Html::checkbox( [ str_pad("Tag_1 ",36,"&nbsp;") =>['technická'=>'technická'], 
                                  str_pad("Tag_2 ",36,"&nbsp;") =>['manažerská/vedoucí'=>'manažerská/vedoucí'] ] ,
                                [ 'manažerská/vedoucí'=>'manažerská/vedoucí'] ); ?>
            
        </form>     
        <br/> <br/>
<?php } 
?>   
