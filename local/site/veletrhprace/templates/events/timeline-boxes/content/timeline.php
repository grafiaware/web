<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
?>
<form name="event-enroll" action="" method="POST">
                    <div class="timeline">
                        <?= $this->repeat(__DIR__.'/timeline/timeline-point.php', $event) ?>
                    </div>
</form>