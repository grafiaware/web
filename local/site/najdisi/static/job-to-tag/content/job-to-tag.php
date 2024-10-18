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
                <div >
                    <div class="active title">
                        <i class="dropdown icon"></i>
                        <label>Název pozice:</label>
                    </div>
                    <div class="active content">     
                   
                        <input <?= $readonly ?> type="text" name="job-nazev" placeholder="" maxlength="45"
                                                value="<?= isset($jobNazev)?  $jobNazev : '' ?>" >                      
                        
                        <div class="field">
                            <?= Html::checkbox( $allTags , $checkTags ); ?>
                        </div>
                    
                        <button class='ui primary button' type='submit' formaction='events/v1/jobtotag/<?= $jobId ?>' > Uložit </button>
                    </div>        
                </div>
            </div>


        </form>
        <br/> <br/>
<?php }
?>
