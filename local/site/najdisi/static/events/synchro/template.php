<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
?>

        <form class="ui huge form" action="" method="POST" >           
            <div>
                <?=                
                "<button class='ui primary button' type='submit' "
                                . "formaction='".Text::encodeUrlPath("events/v1/synchro")."' > Synchro </button>" 
                ?>     
                <?=                
                "<button class='ui primary button' type='submit' "
                                . "formaction='".Text::encodeUrlPath("events/v1/validateuser")."' > ValidateUser </button>" 
                ?>     
            </div>
            <hr/>
        </form>           
