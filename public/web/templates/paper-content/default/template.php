<?php
    use Model\Entity\PaperContentInterface;
    /** @var PaperContentInterface $paperContent */
?>
<content>
    <div class="paper-content">
        <div class="ui right tiny corner blue label">

        </div>
        <div class="semafor">

        </div>
        <div class="author-text">
            <?= $paperContent->getContent() ?>
        </div>
    </div>
</content>