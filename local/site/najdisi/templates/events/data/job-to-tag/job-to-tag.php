<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */

?>
                        <?php if($checkedTagsText){ ?>
                            <span class="ui big basic red tag label tag-list">                                                                                               
                                <?= implode(', ',array_keys($checkedTagsText) ); ?>                                 
                            </span>     
                        <?php }?>
