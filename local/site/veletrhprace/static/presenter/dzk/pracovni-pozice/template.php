<?php
use Site\Configuration;
use Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Pracovní pozice';
$perex = '';

?>
    <div id="pracovni-pozice">
       <?php
           include Configuration::componentController()['templates']."presenter-job/template.php";
        ?>
    </div>