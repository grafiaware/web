<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>

            <?= $addHeadline ?? false ? "<p>$addHeadline</p>" : "" ?>

        <form class="ui huge form" action="" method="POST" >
            <!--<div class="two fields">-->   
                <?= $this->insertIf(!($editable ?? false), $fieldsTemplate, $fields  ?? [], __DIR__.'/noData.php') ?>
                <?= $this->insertIf($editable ?? false, $fieldsTemplate, $fields  ?? []) ?>
            <!--</div>-->                
            <!--buttons-->
            <div class="text okraje-dole">
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
                ($editable ?? false) && ($remove ?? false) ? "<button class='ui primary button' type='submit' formaction='$componentRouteSegment/$id/remove' > Odstranit </button>" : "";
                ?>
            </div>   
        </form>         