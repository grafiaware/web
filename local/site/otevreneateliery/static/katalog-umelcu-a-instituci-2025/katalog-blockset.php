<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
?>
     
<?=  "<p> <a id=\"$pismeno\"></a><b>$pismeno</b></p>"  ?>
<?=  $this->repeat(__DIR__.'/katalog-blockset-row.php', $klienti) ?>  
