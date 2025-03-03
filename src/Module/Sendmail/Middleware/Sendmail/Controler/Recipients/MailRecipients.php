<?php
namespace Sendmail\Middleware\Sendmail\Controler\Recipients;

use Sendmail\Middleware\Sendmail\Campaign\CampaignConfigInterface;

use Sendmail\Middleware\Sendmail\Controler\Recipients\MailRecipientsInterface;
use Sendmail\Middleware\Sendmail\Controler\Recipients\RecipientsValidatorInterface;
use Sendmail\Middleware\Sendmail\Controler\Recipients\ValidityEnum;

use Pes\Debug\Timer;

use CallbackFilterIterator;
use ArrayIterator;
use SplFileObject;

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
    
    /**
     * parametry:
     * vstupní csv, verified csv, email callback => closure, která z řádky dat vrací email adresu příjemce, user callback => closure, která z řádky dat vrací jméno příjemce
     * 
     * - načte vstupní csv, načte verified csv
     *  - adresy, které jsou ve vstupním csv a nejsou ve verified csv verifikuje a přidá do verified csv
     *  - adresy, které již jsou i ve verified csv verifikuje, pokud požadovaný stupeň verifikace je vyšší než stupeň, na který byla adresa již verifikována a přepíše údaje ve verified csv
     * 
     * stupeň verifikace:
     * hodnota z ValidationDegreeEnum
     */


    public function validateSourceFile(CampaignConfigInterface $campaignConfig) {
        $emailCallback = $campaignConfig->getEmailCallback();
        $validationDegree = $campaignConfig->getValidationDegree();
        
        // tady precist soubor s daty a verifikovat
        $data = $this->importCsv($campaignConfig->getSourceCsvFilepath());
        $timer = new Timer();
        $startSeconds = $timer->start();
        foreach ($data as $dataRow) {
            $emailAddress = $emailCallback($dataRow);
            if (is_string($emailAddress)) {
                $validAddress = $this->recipientsValidator->validateEmail($emailAddress, $validationDegree);                       
            } else {
                $validAddress = ValidityEnum::NO_MAIL;
            }
            $validatedData[] = array_merge($dataRow, ['mail address validity' => $validAddress, 'validation time'=> date("Y-m-d H:i:s")]);
        }
        
        $this->exportCsv($campaignConfig->getValidatedCsvFilepath(), $validatedData);
    }
   
        
    public function getRecipients(CampaignConfigInterface $campaignConfig) {
        $emailCallback = $campaignConfig->getEmailCallback();
        $userCallback = $campaignConfig->getUserCallback();
        
        $validatedData = $this->importCsv($campaignConfig->getSourceCsvFilepath());
        $filteredData = new CallbackFilterIterator(new ArrayIterator($validatedData), $campaignConfig->getFilterCallback());
        $recipientsData = [];
        foreach ($filteredData as $row) {
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

   
    

    
    
    
    
