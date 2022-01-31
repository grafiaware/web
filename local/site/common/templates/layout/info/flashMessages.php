<?php
use Pes\Text\Text;
use Pes\Text\Html;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

echo $this->repeat(__DIR__."/flashMessage.php", $flashMessages);