
        <div class="sixteen wide column padding-vertical">
            <?= $this->insert(__DIR__.'/rozvrzeni-variant/ukazka-loga.php', $logo['ukazkaLoga'])?> 
            <?= $this->repeat(__DIR__.'/rozvrzeni-variant/stahnout-logo.php', $logo['stazeniLoga']) ?>
        </div>