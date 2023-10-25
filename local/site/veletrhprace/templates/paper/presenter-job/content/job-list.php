<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
?>
 
<div class="vypis-prac-pozic">
    <div class="ui styled fluid accordion">
        <?= $this->repeat(__DIR__.'/vypis-pozic/job.php', $jobs  )?>
    </div>
</div>


