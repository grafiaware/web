<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

?>
                        <?php if($checkedTagsText){ ?>
                            <span class="ui big red tag label tag-list">                                                                                               
                                <?= implode(', ',array_keys($checkedTagsText) ); ?>                                 
                            </span>     
                        <?php }?>
