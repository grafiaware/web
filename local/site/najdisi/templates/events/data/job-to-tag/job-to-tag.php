<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

?>
                        <div class="field">                                                                                               
                                <?= implode(', ',array_keys($checkedTagsText) ); ?>                                 
                        </div>                                                
