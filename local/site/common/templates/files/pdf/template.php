<?php
use Site\ConfigurationCache;

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

//    $uriInfo = $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME);
//    $basePath = $this->getUriInfo($request)->getSubdomainPath();
    
if (PES_RUNNING_ON_PRODUCTION_HOST) {
    $basePath = "/";
} else {
    $basePath = "/web/";
}
// volá se z presenter/20XX/template
$filePath;  // _files/... relativní adresa vůči documentroot
$file;

$absoluteUrl = $basePath.$filePath;
$toolbar = "#toolbar=0&navpanes=0";  // skryje panel (ne všude, ale často funguje) - v Opera, Edge ano, ve Firefox ne

?>

<iframe
  src="public/assets/pdfjs/scroll/simple.html?file=<?= $absoluteUrl.$file ?>"
  width="100%"
  height="800">
</iframe>

