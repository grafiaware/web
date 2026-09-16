<?php
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
echo Html::tag('div', ['data-info'=>'flash'], $this->repeat(__DIR__."/flashMessage.php", $flashMessages)) ;