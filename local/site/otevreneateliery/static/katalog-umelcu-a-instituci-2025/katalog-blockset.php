<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */
?>
     
<?=  "<p> <a id=\"$pismeno\"></a><b>$pismeno</b></p>"  ?>
<?=  $this->repeat(__DIR__.'/katalog-blockset-row.php', $klienti) ?>  
