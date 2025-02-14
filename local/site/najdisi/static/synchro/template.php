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

            

            <?php
                    $controlledItems = ["AndyAndy",	"Andy_/Akka/",	"CvicnyRepre",	"events_administrator",
                         "Kralik", "navstevnik", "navstevnik1", "representative","visitor", "vlse2610" ];
                    foreach ($controlledItems as $key => $value) {
            ?>
                          <?=  
                            "<input name='controlledItems[".$key."]' value='$value' >"
                          ?> 
            <?php
                    }
            
            if($readonly === '') {
            ?>
                                   
            <div>
                <?=                
                "<button class='ui primary button' type='submit' "
                                . "formaction='".Text::encodeUrlPath("events/v1/synchro")."' > Synchro </button>" 
                ?>                                                                                                                                                                                                                                                 
            </div>
            </></p>
            <div>
                <?=                
                "<button class='ui primary button' type='submit' "
                                . "formaction='".Text::encodeUrlPath("auth/v1/ladimsynchro")."' > ladim Synchro - zakladna dat</button>" 
                ?>                                                                                                                                                                                                                                                 
            </div>
            
            <?php
            }
            ?>

        </form>           
