<?php
use Site\Configuration;

$headline = 'Pracovní pozice';
$perex = '';

?>
    <div id="pracovni-pozice">
       <?php
        include Configuration::componentController()['templates']."paper/presenter-job.php";
        ?>
    </div>