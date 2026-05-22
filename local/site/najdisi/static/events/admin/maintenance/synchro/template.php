<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */
?>

        <form class="ui huge form" action="" method="POST" >           
            <div>
                <?=                
                "<button class='ui primary button' type='submit' "
                                . "formaction='".Text::encodeUrlPath("events/v1/synchro")."' > Synchro </button>" 
                ?>                                                                                                                                                                                                                                                 
            </div>
            <hr/>
        </form>           
