    <script type="text/javascript" ><?= $tinyConfigView ?></script>

    <script type="text/javascript" src=" <?= $urlTinyMCE ?>" referrerpolicy="origin"></script>
    <!--<script type="module" src=" <?= $urlTinyInit ?>" ></script>-->
<?php if (!empty($urlTitleScript)): ?>
    <script type="module" src=" <?= $urlTitleScript ?>" defer></script>
<?php endif; ?>
    <script type="module" src=" <?= $urlEditScript ?>" defer></script>
