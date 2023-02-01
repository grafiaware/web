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
                        <hr/><br/><br/>                                  
                </div>          
            </div>                  
        </form>     
<?php }  ?>   

 
    
<?php         
  //  Html::checkbox($checkboxsetLabelsNameValuePairs, $context);
   $pp = ['technická'=>'technická', 'manažerská/vedoucí'=>'manažerská/vedoucí'];
   // Html::checkbox($pp , ['technická', 'manažerská/vedoucí'] );
    MojeHtml::checkbox($pp , ['technická', 'manažerská/vedoucí'] );        
 ?>  


    <!--    < ?php
            if($readonly === '') { ?>
            <div>
                < ?=
                 isset($jobId) ?                 
                "<button class='ui primary button' type='submit' formaction='events/v1/jobtotag/". $jobTagTag . "/" . $jobId  ."/remove' > Odstranit </button>" :    
                "<button class='ui primary button' type='submit' formaction='events/v1/jobtotag' > Uložit </button>" ;                    
    
                 isset($companyId) ?                 
                "<button class='ui primary button' type='submit' formaction='events/v1/representative/". $loginLoginName . "/" . $companyId  ."/remove' > Odstranit representanta </button>" :    
                "<button class='ui primary button' type='submit' formaction='events/v1/representative' > Uložit </button>" ;                
               
                ?>                                                                                                                                                                                                                                                 
            </div>
            < ?php }  ?>
    -->
      