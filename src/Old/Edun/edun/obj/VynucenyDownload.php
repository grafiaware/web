<?php

/*
 * Třída s požitím http hlaviček vynutí v prohlížečí dialog pro otevření/uložení souboru
 * Současně vyprázdní výstupní buffer a ukončí vykonávání skriptu, v prohlížeči tedy zůstane zobrazena minulá stránka
 * @param string název souboru s cestou
 */
class VynucenyDownload
{
    
    
    
 public static function download($souborProDownload)        { 
 if (file_exists($souborProDownload)) { 
     $o=ob_clean();
     
     ob_start();
     $o=ob_clean();
     $o=ob_flush(); //??
          
     header("Content-Description: File Transfer"); 
     header("Content-Type: application/force-download"); 
     header("Content-Disposition: attachment; filename=" . basename($souborProDownload) ); 

     readfile ($souborProDownload); 
     //ob_flush(); 
     
     exit;
     
     // pri Otevrit nebo neni-li vyse exit- vypise obsah bufferu a ev.dale nasledujici radek a dalsi vystupy generovane dale v kodu
     //echo "<br>**********************************************"; 
     
                
 }   
}   
    
    
    
    
        public static function download_PS($souborProDownload)
        { 
            if (file_exists($souborProDownload)) {
                               
                
                header('Content-Description: File Transfer');
                //header("Content-type: application/force-download"); 
                //header('Content-Type','text/html; charset=windows-1250');
                //header('Content-type: application/pdf');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($souborProDownload).'"');
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($souborProDownload));
                $o=ob_clean();
                $o=ob_flush();
                readfile($souborProDownload);
                exit;
            }
        }
//function downloadFile( $fullPath ){
//
//  // Must be fresh start
//  if( headers_sent() )
//    die('Headers Sent');
//
//  // Required for some browsers
//  if(ini_get('zlib.output_compression'))
//    ini_set('zlib.output_compression', 'Off');
//
//  // File Exists?
//  if( file_exists($fullPath) ){
//   
//    // Parse Info / Get Extension
//    $fsize = filesize($fullPath);
//    $path_parts = pathinfo($fullPath);
//    $ext = strtolower($path_parts["extension"]);
//   
//    // Determine Content Type
//    switch ($ext) {
//      case "pdf": $ctype="application/pdf"; break;
//      case "exe": $ctype="application/octet-stream"; break;
//      case "zip": $ctype="application/zip"; break;
//      case "doc": $ctype="application/msword"; break;
//      case "xls": $ctype="application/vnd.ms-excel"; break;
//      case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
//      case "gif": $ctype="image/gif"; break;
//      case "png": $ctype="image/png"; break;
//      case "jpeg":
//      case "jpg": $ctype="image/jpg"; break;
//      default: $ctype="application/force-download";
//    }
//
//    header("Pragma: public"); // required
//    header("Expires: 0");
//    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//    header("Cache-Control: private",false); // required for certain browsers
//    header("Content-Type: $ctype");
//    header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" );
//    header("Content-Transfer-Encoding: binary");
//    header("Content-Length: ".$fsize);
//    ob_clean();
//    flush();
//    readfile( $fullPath );
//
//  } else
//    die('File Not Found');
//
//}         
}
?>
