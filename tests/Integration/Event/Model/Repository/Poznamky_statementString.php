

" SELECT `id`, `document`, `document_filename`, `document_mimetype`  
FROM `document`  WHERE document_filename LIKE "C:\APACHEROOT\web\tests\Integration\Event\Model\Repository%" "
  

!!!!!!!!!!!! OESCAPOVANI     ZPETNYCH LOMITEK  PRO MYSQL !!!!!!!!!!!!!!!!!!!!!!!!
    
 SELECT `id`, `document`, `document_filename`, `document_mimetype`  
 FROM `document`  WHERE document_filename LIKE  'C:\\\\APACHEROOT\\\\web\\\\tests\\\\Integration\\\\Event\\\\Model\\\\\Repository%'   
 
 //mysqli_real_escape_string, nutne mit dostupne mysqli_connect
 //       $dir1 = __DIR__ ."%";
 //       $dir2 = \mysqli_real_escape_string( ... mysqli_connect..., $dir1 );
 //       $escapedString =  "document_filename LIKE '". \mysqli_real_escape_string( $dir1 ) . "'"; 