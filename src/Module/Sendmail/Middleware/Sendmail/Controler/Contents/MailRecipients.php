<?php

namespace Sendmail\Middleware\Sendmail\Controler\Contents;

use Site\ConfigurationCache;

use Sendmail\Middleware\Sendmail\Controler\Contents\MailRecipientsInterface;
use Sendmail\Middleware\Sendmail\Controler\Contents\RecipientsValidatorInterface;

use SplFileObject;
use UnexpectedValueException;



/**
 * 
 */
class MailRecipients implements MailRecipientsInterface {
    /** @var RecipientsValidatorInterface $recipientsValidator */
    private $recipientsValidator;
       
    public function __construct( 
                RecipientsValidatorInterface $recipientsValidator 
        ) {
            $this->recipientsValidator = $recipientsValidator;               
    }
    
    


    public function getRecipients($jmenoSouboruCSV) {
        // tady precist soubor s daty a verifikovat
        $data = $this->importCsv ( __DIR__ . "/"  . $jmenoSouboruCSV .".csv");
        
      //  $verifiedData = $this->recipientsValidator->validate($data);                       
      //  exportCsv($jmenoSouboruCSV ."_verified.csv", $verifiedData);
        $this->exportCsv( __DIR__ . "/"  . $jmenoSouboruCSV ."_verified.csv", $data);
   
        
        //-------------------------------------------------------------------------------
        //  ***** prectu nahradni
        $verifiedDataFromFile = $this->importCsv( __DIR__ . "/"  . $jmenoSouboruCSV . "_verifiedN.csv");                        
        $verifiedData = $verifiedDataFromFile;
        // **********  tady vybrat z dat ty s  user **************
        $recipientsData = [];
        foreach ($verifiedData as $key => $row) {
             if ($row['mail verified'] == 'user' ) {
                $recipientsData[] =  [ 'email' => $row['E-mail:'], 
                                       'prijmeni' => $row['Příjmení'] ];
             }            
        }
   
        return $recipientsData;
            
    }
    
    
    
    private function importCsv($filename) {
        $file = new SplFileObject(  $filename, "r");
        $file->setFlags(SplFileObject::READ_CSV);
        $first = true;
        foreach ($file as $row) {
            if ($first) {
                $headers = array_map(function($val){return iconv("Windows-1250", "UTF-8//IGNORE", $val);}, $row);
                $first = false;
            } else {
                $utf8row = array_map(function($val){return iconv("Windows-1250", "UTF-8//IGNORE", $val);}, $row);
                if ($utf8row[0]) {
                    $data[] = array_combine($headers, $utf8row); // Spojí hlavičku s daty
                }
            }
        }
        return $data;
    }

    
    
    function exportCsv($filename, array $data) {
        $file = new SplFileObject($filename, "w");
        $headers = array_map(function($val){return iconv("UTF-8", "Windows-1250", $val);}, array_keys($data[0]));
        $file->fputcsv($headers);
        $first = true;
        foreach ($data as $dataRow) {
            if ($first) {
                $first = false;
            } else {
                $row = array_map(function($val){return iconv("UTF-8", "Windows-1250", $val);}, $dataRow);
                $file->fputcsv($row);
            }
        }
    }
  
    
} 
    
    
//    
//    private function exportCsv($filename, array $data) {
//        $file = new SplFileObject($filename, "w");
//        $headers = array_keys($data);
//        $file->fputcsv($headers);
//        foreach ($data as $dataRow) {
//            $row = array_map(function($val){return iconv("UTF-8", "Windows-1250", $val);}, $dataRow);
//            $file->fputcsv($row);
//        }
//    }

   
    

    
    
    
    
