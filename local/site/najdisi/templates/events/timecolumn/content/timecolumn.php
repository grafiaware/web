<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Text;
/** @var PhpTemplateRendererInterface $this */

?>

<form name="event-enroll" action="" method="POST">
                        <?= $this->repeat(__DIR__.'/timecolumn/timerow.php', $event) ?>
</form>
