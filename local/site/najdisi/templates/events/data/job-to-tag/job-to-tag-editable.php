<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
if ($editable) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }   
?>

<?php if ( (isset($jobNazev)) ) { ?>
        <form class="ui huge form" action="" method="POST" >
            <div class="fields">
                <div >
                    <div class="active title">
                        <i class="dropdown icon"></i>
                        <label>Název pozice:</label>
                    </div>
                    <div class="active content">     
                   
                        <input <?= $readonly ?> type="text" name="job-nazev" placeholder="" maxlength="120"
                                                value="<?= isset($jobNazev)?  $jobNazev : '' ?>" >                      
                        
                        
                        <?php if ( $editable ) { ?>
                            <div class="field">
                                 <?= Html::checkbox( $allTags , $checkedTags ); ?>
                            </div>    
                            <button class='ui primary button' type='submit' formaction='events/v1/jobtotag/<?= $jobId ?>' > Uložit </button>

                            <?php }else { ?>
                                <div class="field">                                                                                               
                                        <?= implode(', ',array_keys($checkedTagsText) ); ?>                                 
                                </div>                         
                            <?php } ?>
                       
                    
                    </div>        
                </div>
            </div>


        </form>
        <br/> <br/>
<?php }
?>
