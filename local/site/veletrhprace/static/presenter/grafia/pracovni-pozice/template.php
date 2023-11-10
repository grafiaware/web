<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

$headline = 'Pracovní pozice';
$perex = '';

?>
    <div id="pracovni-pozice">
       <?php
        include ConfigurationCache::eventTemplates()['templates']."paper/presenter-job.php";
        ?>
    </div>