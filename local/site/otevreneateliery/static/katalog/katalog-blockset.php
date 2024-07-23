<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
?>
     
<?=  "<div id=\"$pismeno\"> $pismeno </div>"  ?>
<?=  $this->repeat(__DIR__.'/katalog-blockset-row.php',  $klienti) ?>  