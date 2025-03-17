<?php
namespace Sendmail\Middleware\Sendmail\Recipients;

use Sendmail\Middleware\Sendmail\Campaign\CampaignConfigInterface;

use Sendmail\Middleware\Sendmail\Recipients\MailRecipientsInterface;
use Sendmail\Middleware\Sendmail\Recipients\RecipientsValidatorInterface;
use Sendmail\Middleware\Sendmail\Csv\CampaignDataInterface;
use Sendmail\Middleware\Sendmail\Recipients\MailSenderInterface;

use Pes\Debug\Timer;

use UnexpectedValueException;
use RuntimeException;

/**
 * 
 */
class MailRecipients implements MailRecipientsInterface {
    
    /**
     *  @var RecipientsValidatorInterface
     */
    private $recipientsValidator;
    
    /**
     * @var CampaignDataInterface
     */
    private $campaignData;


    public function __construct( 
            RecipientsValidatorInterface $recipientsValidator,
            CampaignDataInterface $campaignData
        ) {
        $this->recipientsValidator = $recipientsValidator;
        $this->campaignData = $campaignData;
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
     * hodnota z ValidityEnum
     */


    public function appendSourceCsv(CampaignConfigInterface $campaignConfig): int {
        $sourceData = $this->campaignData->importSourceCsvFile($campaignConfig);
        try {
            // pro neexistující soubor vyhodí RuntimeException
            $targetData = $this->campaignData->importTargetCsvFile($campaignConfig);            
        } catch (RuntimeException $exc) {   // message: no such file or directory
            $targetData = [];
            // není cílový soubor - pokračuji dál, soubor bude založe v appendToCsv
        }
        if (!empty($sourceData) && !empty($targetData) && (count(array_keys(reset($sourceData))) !== count(array_intersect_key(reset($sourceData),reset($targetData))))) {
            // porovná klíče prvních položek dat
            throw new UnexpectedValueException("Unable to append source file content. Source and target file has diferent headers.");
        }
        $appendedData = [];
        foreach ($sourceData as $id => $dataRow) {
            if (!array_key_exists($id, $targetData)) {
                $appendedData[$id] = array_merge($dataRow, [
                    MailRecipientsInterface::ADDITION_TIME => date("Y-m-d H:i:s"), 
                    MailRecipientsInterface::MAIL_ADDRESS_VALIDITY => '', 
                    MailRecipientsInterface::VALIDATION_TIME=> '', 
                    MailSenderInterface::CAMPAIGN_ASSEMBLY => '',
                    MailSenderInterface::SENDING_TIME => ''
                ]);
            }
        }
        if ($appendedData) {
            $this->campaignData->appendToTargetCsvFile($campaignConfig, $appendedData);
        }
        return count($appendedData);
    }
    
    /**
     * 
     * Pokud proběhla validace dat vrací pole s daty řádků csv souboru, 
     * pokud neproběhla validace - nepodařilo se provést ani jednu validaci (např. z důvodu chyby nebo krátkého timeoutu apod.) vrací false, 
     * pokud neproběhla validace protože všechny řádky v csv souboru již byly validovány, vrací true.
     * 
     * Když byl řádek již jednou validován, není znovu validován i když je metoda zavolána s vyšším požadovaným stupněm validace.
     * 
     * @param CampaignConfigInterface $campaignConfig
     * @param numeric $maxRuntime
     * @return array|bool 
     * @throws UnexpectedValueException
     */
    public function validateEmailsInCsvFile(CampaignConfigInterface $campaignConfig, $maxRuntime=10): array {
        $targetData = $this->campaignData->importTargetCsvFile($campaignConfig);
        $emailCallback = $campaignConfig->getEmailCallback();
        $validationDegree = $campaignConfig->getValidationDegree();
        
        // tady precist soubor s daty a verifikovat
        $timer = new Timer();
        $startSeconds = $timer->start();
        $validatedData = [];
        $rowsValidated = 0;
        $rowsSkippedAsValid = 0;
//        $filteredTargetData = new CallbackFilterIterator(new ArrayIterator($targetData), function($dataRow){return empty($dataRow['mail address validity']);});
        $rowsTotal = count($targetData);
        foreach ($targetData as &$dataRow) {  // použití reference - umožňuje měnit data v poli v průběhu cyklu
            if ($timer->runtime()>$maxRuntime) {
                break;
            }
//            if ($rowsValidated>2) {
//                break;
//            }
            if($dataRow[MailRecipientsInterface::MAIL_ADDRESS_VALIDITY]==='') {
                $emailAddress = $emailCallback($dataRow);
                $validity = $this->recipientsValidator->validateEmail($emailAddress, $validationDegree);                       
                $dataRow[MailRecipientsInterface::MAIL_ADDRESS_VALIDITY] = $validity;
                $dataRow[MailRecipientsInterface::VALIDATION_TIME] = date("Y-m-d H:i:s");
                $validatedData[] = $dataRow;
                $rowsValidated++;
            } else {
                $rowsSkippedAsValid++;
            }
        }

        $this->campaignData->exportTargetCsvFile($campaignConfig, $targetData);
        return 
        [
            'Total number of rows' => $rowsTotal,
            'Number of rows skipped as valid' => $rowsSkippedAsValid,
            'Number of rows validated' => $rowsValidated,
            'Runtime' => $timer->runtime().' seconds',
            'Validated data' => $validatedData
        ];
        
    }
    

} 
    
    


   
    

    
    
    
    
