<?php
use Site\ConfigurationCache;

$headline = 'Pracovní pozice';
$perex = '';

?>
    <div id="pracovni-pozice">
       <?php
        include ConfigurationCache::eventTemplates()['templates']."paper/presenter-job.php";
        ?>
    </div>