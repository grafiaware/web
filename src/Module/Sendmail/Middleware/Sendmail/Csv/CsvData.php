<?php
namespace Sendmail\Middleware\Sendmail\Csv;

use Sendmail\Middleware\Sendmail\Csv\CsvDataInterface;
use SplFileObject;

/**
 * Description of CsvData
 *
 * @author pes2704
 */
class CsvData implements CsvDataInterface {
    
    public function importCsv($filepath, $csvFileRowIdCallback): array {
        $file = new SplFileObject($filepath, "r");
        $file->setFlags(SplFileObject::READ_CSV);
        $first = true;
        foreach ($file as $row) {
            if ($first) {
                $headers = $this->convertToWin1250($row);
                $first = false;
            } else {
                $utf8row = $this->convertToWin1250($row);
                if ($utf8row[0]) {
                    $assoc = array_combine($headers, $utf8row); // Spojí hlavičku s daty
                    $data[$csvFileRowIdCallback($assoc)] = $assoc;
                }
            }
        }
        unset($file);   // zavře objekt
        return $data;
    }
    
    public function appendToCsv($filepath, array $appendedData): void {
        if (empty($appendedData)) {
            return;
        }
        $file = new SplFileObject($filepath, "a+");
        $size = $file->fstat()['size'];
        if ($size==0) {
            $headers = $this->convertToUtf8(array_keys(reset($appendedData)));
            $file->fputcsv($headers);
        }
        foreach ($appendedData as $dataRow) {
            $row = $this->convertToUtf8($dataRow);
            $file->fputcsv($row);
        }
        unset($file);   // zavře objekt        
    }
    
    /**
     * {@inheritDoc}
     * 
     * <p>Jméno zálohy zálohy je složeno z jména zálohovaného souboru, řetězce "_backup" a času zálohy ve formátu 'Ymd_His'.</p>
     * 
     * @param string $filepath <p>Path to the source file.</p>
     * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
     */
    public function backupCsvFile($filepath): bool {
        // Vytvoření zálohy CSV souboru
        $backupPath = str_replace('.csv', '', $filepath).'_backup_' . date('Ymd_His') . '.csv';
        return copy($filepath, $backupPath);        
    }
    
    public function replaceTargetCsvFile($filepath, array $data): void {
        $file = new SplFileObject($filepath, "w");
        $file->rewind();
        $file->ftruncate(0);
        $headers = $this->convertToUtf8(array_keys(reset($data)));
        $file->fputcsv($headers);
        foreach ($data as $dataRow) {
            $row = $this->convertToUtf8($dataRow);
            $file->fputcsv($row);
        }
        unset($file);   // zavře objekt        
    }
    
    private function convertToUtf8($dataRow) {
        return array_map(function($val){return iconv("UTF-8", "Windows-1250", $val);}, $dataRow);
    }
    
    private function convertToWin1250($dataRow) {
        return array_map(function($val){return iconv("Windows-1250", "UTF-8//IGNORE", $val);}, $dataRow);
    }
    
}
