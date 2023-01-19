<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

    //    $readonly = 'readonly="1"';
    //    $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = ''; 
?>

        <form class="ui huge form" action="" method="POST" >      

            <div class="three fields">                        
                <!-- <div class="field">
                <label>Job Id</label>
                    <input < ?= $readonly ?> type="text" name="job-id" placeholder="" maxlength="10" value="< ?= isset($jobId)?  $jobId : '' ?>">
                </div>  -->
                
                <div class="field">
                    <?php if (isset($jobNazev) ){ ?>
                            <label>jobNazev</label>
                            <input <?= $readonly ?> type="text" name="job-nazev" placeholder="" maxlength="45" value="<?= isset($jobNazev)?  $jobNazev : '' ?>">
                    <?php } else { ?>
                            <?= Html::select("selectJob", "Job nazev:", $selectJobs, [], []) ?>                      
                    <?php } ?>    
                </div>
                
                <div class="field"> 
                    <?php  if (isset($jobTagTag) ) { ?>
                            <label>jobTagTag</label>
                            <input <?= $readonly ?> type="text" name="job-to-tag" placeholder="" maxlength="50" value="<?= isset($jobTagTag)? $jobTagTag : '' ?>">
                    <?php } else { ?>
                            <?= Html::select("selectJobTag", "Tag:",  $selectjobTags, [], []) ?>   
                    <?php } ?> 
                </div>
            </div>
                    

            <?php
            if($readonly === '') {
            ?>
            <div>
                <?=
                 isset($jobId) ?                 
                "<button class='ui primary button' type='submit' formaction='events/v1/jobtotag/". $jobTagTag . "/" . $jobId  ."/remove' > Odstranit </button>" :    
                "<button class='ui primary button' type='submit' formaction='events/v1/jobtotag' > Uložit </button>" ;                
                ?>                                                                                                                                                                                                                                                 
            </div>
            <?php
            }
            ?>

        </form>           
