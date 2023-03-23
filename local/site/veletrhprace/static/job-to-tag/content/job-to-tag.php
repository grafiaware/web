<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Entity\JobTagInterface;
use Events\Model\Repository\JobTagRepo;


/** @var PhpTemplateRendererInterface $this */

    //    $readonly = 'readonly="1"';
    //    $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = '';                              
?>

<?php if ( (isset($jobNazev)) ) { ?>
        <form class="ui huge form" action="" method="POST" >      
            <div class="fields">                        
                
                <!-- <div class="field">
                <label>Job Id</label>
                    <input < ?= $readonly ?> type="text" name="job-id" placeholder="" maxlength="10" value="< ?= isset($jobId)?  $jobId : '' ?>">
                </div>  -->               
                
                <div class="field">                                                   
                        <label>Název pozice:</label>
                        <input <?= $readonly ?> type="text" name="job-nazev" placeholder="" maxlength="45" 
                                                value="<?= isset($jobNazev)?  $jobNazev : '' ?>" >                       
                        <!--  <? = Html::checkbox( $allTagsStrings , $checkTagsStrings );  ?>   -->
                         <?= Html::checkbox( $allTags , $checkTags ); ?>   
                        
                        <button class='ui primary button' type='submit' formaction='events/v1/jobtotag/<?= $jobId ?>' > Uložit </button> 
                </div>          
            </div> 
        
        
        </form>     
        <br/> <br/>
<?php } 
?>   
