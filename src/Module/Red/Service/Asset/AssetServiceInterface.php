<?php
namespace Red\Service\Asset;

use Psr\Http\Message\UploadedFileInterface;

/**
 *
 * @author pes2704
 */
interface AssetServiceInterface {
    /**
     * Uloží soubor do cílového místa pro uploadované soubpry a zapíše do databáze informace o assetu.
     * 
     * @param UploadedFileInterface $uploadedFile Soubor přijaty v http requestu (uploadovaný)
     * @param string $editedItemId Id položky menu item, ve které je soubor jako asset použit
     * @param string $editor
     * @return string Cesta k uloženému souboru ve tvaru vhodném jako odkaz do html - relativní cesta vzhledem k rootu (t.j. např. hodnota pro <img src="cesta")
     */
    public function storeAsset(UploadedFileInterface $uploadedFile, string $editedItemId, string $editor): string;
            
}
