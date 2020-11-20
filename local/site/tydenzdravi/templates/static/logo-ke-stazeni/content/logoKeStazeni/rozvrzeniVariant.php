
        <div class="sixteen wide column padding-vertical">
            <?= $this->insert(__DIR__.'/rozvrzeniVariant/ukazkaLoga.php', $logo['ukazkaLoga'])?> 
            <?= $this->repeat(__DIR__.'/rozvrzeniVariant/stahnoutLogo.php', $logo['stazeniLoga']) ?>
        </div>