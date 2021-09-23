<?php
$onloadScript = "replaceElement('red_loaded_$loaderWrapperElementId', '$apiUri');"
?>
<div id="red_loaded_<?=$loaderWrapperElementId?>" class="red_loaded">
    <script>
        replaceElement("red_loaded_<?=$loaderWrapperElementId?>", "<?= $apiUri?>");
    </script>
</div>
