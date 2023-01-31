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
<?php
    if (isset($seznam)) {
?>           
        <span class=""><?= $seznam . "&nbsp;". "&nbsp;". "&nbsp;" ?></span>                              
<?php
    }
    else { }
?>   
