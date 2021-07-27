<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\View\Renderer\ClassMap\ClassMapInterface;
use Component\Renderer\Html\Authored\Paper\ElementWrapper;
use Component\Renderer\Html\Authored\Paper\Buttons;
use Red\Model\Entity\PaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ClassMapInterface $classMap */
/** @var ElementWrapper $elementWrapper */
/** @var Buttons $buttons */
/** @var PaperContentInterface $paperContent */
?>
                <div class="ui card">
                    <content id="" class="">
                        <div class="content">
                            <p><i class="user circular icon"></i> Tomáš Lebenhart</p> 
                        </div>
                        <div class="content center aligned">
                            <img class="card-image" src="contents/images/svlekl.jpg">
                        </div>
                        <div class="content">
                            <?= $paperContent ?>
                        </div> 
                        <div class="extra content">
                            <p> <i class="large barcode icon"></i> ISBN: 978-80-87046-43-2</p>
                        </div>
                    </content>
                </div>