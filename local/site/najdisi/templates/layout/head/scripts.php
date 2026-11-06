<?= "";//<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous" ></script> ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<?php // záloha při nedostupném CDN (offline vývoj, cloud): stejná verze z vlastního serveru; bez integrity –
      // vlastní původ SRI nepotřebuje a Git na Windows (core.autocrlf) by změnou konců řádků hash rozbil ?>
<script>window.jQuery || document.write('<script src="<?= $linksCommon.'js/lib/jquery-3.6.0.min.js' ?>"><\/script>');</script>
<?= $redScripts ?? '' ?>
<script type="text/javascript" src="<?= $linksCommon.'js/head.js'?>" ></script>
<script type="text/javascript" src="<?= $linksSite.'semantic-ui/semantic.min.js'?>" ></script>
<script type="module" src="<?= $linksCommon.'js/cookieconsent/consent.js'?>" ></script>
