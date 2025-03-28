<?php
namespace Sendmail\Middleware\Sendmail\Csv;

use Sendmail\Middleware\Sendmail\Csv\CampaignDataInterface;
use Sendmail\Middleware\Sendmail\Campaign\CampaignConfigInterface;

use UnexpectedValueException;
use RuntimeException;

/**
 * Description of CampaignData
 *
 * @author pes2704
 */
class CampaignData implements CampaignDataInterface {
    
    /**
     * @var CsvDataInterface
     */
    private $csvData;

    public function __construct(
            CsvDataInterface $csvData
    ) {
        $this->csvData = $csvData;
    }
    
    public function importSourceCsvFile(CampaignConfigInterface $campaignConfig): array {
        try {
            return $this->csvData->importCsv($campaignConfig->getSourceCsvFilepath(), $campaignConfig->getCsvFileRowIdCallback());
        } catch (RuntimeException $exc) {   // message: no such file or directory
            throw new UnexpectedValueException("Nenalezen vestupní csv soubor uvedený v konfiguraci kampaně.", 0, $exc);
        }        
    }
    
    public function importTargetCsvFile(CampaignConfigInterface $campaignConfig): array {
        try {
            return $this->csvData->importCsv($campaignConfig->getValidatedCsvFilepath(), $campaignConfig->getCsvFileRowIdCallback());        
        } catch (RuntimeException $exc) {   // message: no such file or directory
            throw new UnexpectedValueException("Nenalezen výstupní csv soubor uvedený v konfiguraci kampaně.", 0, $exc);
        }        
    }
    
    public function appendToTargetCsvFile(CampaignConfigInterface $campaignConfig, $appendedData): void {
        try {
            $this->csvData->appendToCsv($campaignConfig->getValidatedCsvFilepath(), $appendedData);
        } catch (RuntimeException $exc) {   // message: no such file or directory
            throw new UnexpectedValueException("Nenalezen výstupní csv soubor uvedený v konfiguraci kampaně.", 0, $exc);
        }          
        
    }
    
    public function exportTargetCsvFile(CampaignConfigInterface $campaignConfig, array $data): void {      
        $filepath = $campaignConfig->getValidatedCsvFilepath();
        // Vytvoření zálohy CSV souboru
        $success = $this->csvData->backupCsvFile($filepath);
        if ($success) {
            // Přepsání cílového souboru novými daty
            try {
                $this->csvData->replaceTargetCsvFile($filepath, $data);
            } catch (RuntimeException $exc) {   // message: no such file or directory
                throw new UnexpectedValueException("Nenalezen výstupní csv soubor uvedený v konfiguraci kampaně.", 0, $exc);
            }              
        } else {
            throw new RuntimeException("Nepodařilo se vytvořit zálohu výstupního csv souboru uvedeného v konfiguraci kampaně.");            
        }
    }
    
}
