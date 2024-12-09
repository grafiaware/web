<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>

        <form class="ui huge form" action="" method="POST" >
            <div class="two fields">   
                <?= $this->insertIf(!($editable ?? false), $fieldsTemplate, $fields  ?? [], __DIR__.'/noData.php') ?>
                <?= $this->insertIf($editable ?? false, $fieldsTemplate, $fields  ?? []) ?>
            </div>                
            <!--buttons-->
            <div>
                <?=
                $editable ?? false ? 
                        "<button class='ui primary button' type='submit' formaction='$componentRouteSegment' > Uložit změny </button>"      
                : "";
                ?>
            </div>   
        </form>         