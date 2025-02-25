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
//                    $controlledItems = ["AndyAndy",	"Andy_/Akka/",	"CvicnyRepre",	"events_administrator",
//                         "Kralik", "navstevnik", "navstevnik1", "representative","visitor", "vlse2610" ];
//                    foreach ($controlledItems as $key => $value) {
            ?>
            <?=  d
//                    "<input name='controlledItems[".$key."]' value='$value' >"
            ?> 
            <?php
//                    }
//          ?>            
<!--       //Text::encodeUrlPath("auth/v1/ladimsynchro").  "' > ladim Synchro - zakladna dat</button>"      -->           

            

            <?php       
                if($readonly === '') {
            ?>
                                   
            <div>
                <?=                
                "<button class='ui primary button' type='submit' "
                                . "formaction='".Text::encodeUrlPath("events/v1/synchro")."' > Synchro </button>" 
                ?>                                                                                                                                                                                                                                                 
            </div>
            <hr/>
            <br/> <br/> <br/> <br/> <br/>
            
                        
            
            <div>
                <?=                
                "<button class='ui primary button' type='submit' "
                                . "formaction='". Text::encodeUrlPath("sendmail/v1/assembly/mail1/campaign/zkouska")
                                                . "' > MailControler.sendCampaign (ze seznamu) </button>" 
                ?>     
                                                                                                                                                                                                                                                           
            </div>
            
            <?php
            }
            ?>

        </form>           
