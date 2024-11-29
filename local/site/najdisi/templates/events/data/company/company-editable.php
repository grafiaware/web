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
                    <label>Název firmy</label>
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="250" value="<?= $name ?? '' ?>" required >
                </div>  
            </div>                
                <!--buttons-->
                <div>
                    <?=
                    $editable ?? false ? 
                        (isset($id) 
                        ?
                            "<button class='ui primary button' type='submit' formaction='$componentRouteSegment/$id' > Uložit změny </button>"
                        :
                            "<button class='ui primary button' type='submit' formaction='$componentRouteSegment' > Přidat </button>" 
                        )        
                    : "";
                    ?>
                    <?=
                    $remove ?? false ? "<button class='ui primary button' type='submit' formaction='$componentRouteSegment/$id/remove' > Odstranit </button>" : "";
                    ?>
                </div>   
        </form>           