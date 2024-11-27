<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

    if ($editable ?? false) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }   
?>

        <form class="ui huge form" action="" method="POST" >

            <div class="two fields">                        
                <div class="field">
                <label><?= $headline ?? "" ?></label>
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="250" value="<?= $name ?? '' ?>" required >
                 </div>  
            </div>                
                <div>
                    <?=
                    $editable ?? false ? 
                        (isset($id) 
                        ?
                            "<button class='ui primary button' type='submit' formaction='events/v1/company/$id' > Uložit změny </button>"
                        :
                            "<button class='ui primary button' type='submit' formaction='events/v1/company' > Uložit </button>" 
                        )        
                    : "";
                    ?>
                    <?=
                    $remove ?? false ? "<button class='ui primary button' type='submit' formaction='events/v1/company/$id/remove' > Odstranit </button>" : "";
                    ?>
                </div>   
        </form>           