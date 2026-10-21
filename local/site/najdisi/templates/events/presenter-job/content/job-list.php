<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */
?>
 
<div class="vypis-prac-pozic">
    <div class="ui styled fluid accordion">
        <?= $this->repeat(__DIR__.'/vypis-pozic/job.php', $jobs  )?>
    </div>
</div>


