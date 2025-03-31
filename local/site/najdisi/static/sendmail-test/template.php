<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Sendmail\Middleware\Sendmail\Campaign\CampaignProviderInterface;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

    //    $readonly = 'readonly="1"';
    //    $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = ''; 
        
        // předělat ->post data (i controler)
        $campaign = CampaignProviderInterface::CAMPAIGN_ANKETY_2025;
?>

        <form class="ui huge form" action="" method="POST" >      
                                   
            <div>
                <button class="ui primary button" type="submit"
                        formaction="<?= Text::encodeUrlPath("sendmail/v1/validate/$campaign")?>"> validate </button>
            </div>
            <div>
                <button class="ui secondary button" type="submit"
                        formaction="<?= Text::encodeUrlPath("sendmail/v1/send/$campaign")?>"> send </button>
            </div>
        </form>           
