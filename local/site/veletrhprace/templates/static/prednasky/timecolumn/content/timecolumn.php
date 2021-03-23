<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */

?>

                        <?= $this->repeat(__DIR__.'/timecolumn/timerow.php', $event) ?>
