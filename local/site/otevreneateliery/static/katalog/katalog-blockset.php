<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

?>

      
<?=  $pismeno   ?>

<?= 
     $this->repeat(__DIR__.'/katalog-blockset-row.php',  $klienti) 
?>
   
<!--<p id="A" style="margin-top: 30px;"><strong>A</strong></p>
<p style="margin-top: 5px;"><a href="web/v1/page/item/6671583cb7df7#ambasador-atelier-markety-pangrac">Ambasador atelier Mark&eacute;ty Pangr&aacute;c</a></p>-->