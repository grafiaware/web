<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

?>

    <div class="" data-template="<?= "bordered_rows" ?>">
        <div class="ui grid">
            <div class="sixteen wide column">
                <section>
                        <?= $headline ?>
                        <?= $perex ?>
                </section>
            </div>
        </div>
        <div class="ui grid stackable centered">
            <?= $this->repeat(__DIR__."/section/bordered_rows.php", $sections, 'paperSection'); ?>
        </div>
    </div>