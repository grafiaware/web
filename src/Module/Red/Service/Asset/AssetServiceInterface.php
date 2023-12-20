<?php
namespace Red\Service\Asset;

use Psr\Http\Message\UploadedFileInterface;

/**
 *
 * @author pes2704
 */
interface AssetServiceInterface {
    /**
     * 
     * @param UploadedFileInterface $uploadedFile
     * @param type $editedItemId
     * @param type $editor
     */
    public function storeAsset(UploadedFileInterface $uploadedFile, $editedItemId, $editor);
            
}
