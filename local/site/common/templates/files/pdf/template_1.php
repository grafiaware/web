<?php
use Site\ConfigurationCache;

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
$path = ConfigurationCache::files()['@presenter']."/smycka/2025/";
$file = "20_Loxxess_7 snímků.pdf";
$toolbar = "#toolbar=0&navpanes=0";  // skryje panel (ne všude, ale často funguje) - v Opera, Edge ano, ve Firefox ne
?>
<iframe
  src="<?= $path.$file.$toolbar ?>"
  width="100%"
  height="800"
  style="border:none"
></iframe>
<embed src="<?= $path.$file ?>" type="application/pdf" width="100%" height="800">
<object data="<?= $path.$file ?>" type="application/pdf" width="100%" height="800">
  <p>PDF nelze zobrazit. <a href="<?= $path.$file ?>">Stáhnout</a></p>
</object>
<iframe
  src="public/assets/pdfjs/web/viewer.html?file=<?= "/web/".$path.$file.$toolbar ?>"
  width="100%"
  height="800">
</iframe>
<iframe
  src="public/assets/pdfjs/scroll/simple.html?file=<?= "/web/".$path.$file ?>"
  width="100%"
  height="800">
</iframe>

