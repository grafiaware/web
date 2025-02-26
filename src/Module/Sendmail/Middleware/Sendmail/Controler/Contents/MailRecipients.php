<?php

namespace Sendmail\Middleware\Sendmail\Controler\Contents;

use Site\ConfigurationCache;


use Psr\Http\Message\ServerRequestInterface;

use Mail\Mail;
use Mail\MessageFactory\HtmlMessage;

use Mail\Params;
use Mail\Params\Content;
use Mail\Params\Attachment;
use Mail\Params\Party;

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
        $data = $this->importCsv($jmenoSouboruCSV .".csv");
        
      //  $verifiedData = $this->recipientsValidator->validate($data);                       
      //  exportCsv($jmenoSouboruCSV ."_verified.csv", $verifiedData);

        exportCsv($jmenoSouboruCSV ."_verified.csv", $data);
        
        
        
        $verifiedDataFromFile = $this->importCsv("VPV_ankety_test_verified.csv");                
        
        //  tady vybrat z dat ty user
        
      
        
        return $verifiedDataFromFile;
            
    }
    
    
    
    private function importCsv($filename) {
        $file = new SplFileObject($filename, "r");
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

    private function exportCsv($filename, array $data) {
        $file = new SplFileObject($filename, "w");
        $headers = array_keys($data);
        $file->fputcsv($headers);
        foreach ($data as $dataRow) {
            $row = array_map(function($val){return iconv("UTF-8", "Windows-1250", $val);}, $dataRow);
            $file->fputcsv($row);
        }
    }

   
    
    }
    
    
    
    
