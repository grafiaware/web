<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

?>
                        <span class="ui big red tag label tag-list">                                                                                               
                                <?= implode(', ',array_keys($checkedTagsText) ); ?>                                 
                        </span>                                                
