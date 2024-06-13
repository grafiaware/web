<?php
use Site\ConfigurationCache;

$headline = 'Pracovní pozice';
$perex = '';

?>
    <div id="pracovni-pozice">
       <?php
        include ConfigurationCache::eventTemplates()['templates']."presenter-job.php";
        ?>
    </div>