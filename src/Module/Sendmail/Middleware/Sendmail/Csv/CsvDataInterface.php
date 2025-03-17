<?php
namespace Sendmail\Middleware\Sendmail\Csv;

/**
 *
 * @author pes2704
 */
interface CsvDataInterface {
    public function importCsv($filepath, $csvFileRowIdCallback): array;
    public function appendToCsv($filepath, array $appendedData): void; 

    /**
     * <p>Vytvoří zálohu csv souboru.</p>
     * 
     * @param string $filepath <p>Path to the source file.</p>
     * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
     */
    public function backupCsvFile($filepath): bool;    
    public function replaceTargetCsvFile($filepath, array $data): void;            
}
