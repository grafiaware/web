<?php
namespace Red\Service\Asset;

use Psr\Http\Message\UploadedFileInterface;
use Site\ConfigurationCache;
use Pes\Utils\Directory;

use Red\Model\Entity\MenuItemAsset;
use Red\Model\Entity\Asset;
use Red\Model\Entity\AssetInterface;
use Red\Model\Repository\MenuItemAssetRepo;
use Red\Model\Repository\AssetRepo;

use RuntimeException;

/**
 * Description of Asset
 *
 * @author pes2704
 */
class AssetService implements AssetServiceInterface {
    
    /**
     * @var MenuItemAssetRepo
     */
    private $menuItemAssetRepo;
    
    /**
     * @var AssetRepo
     */
    private $assetRepo;
    
    public function __construct(
            MenuItemAssetRepo $menuItemAssetRepoRepo,
            AssetRepo $assetRepo) {
        $this->menuItemAssetRepo = $menuItemAssetRepoRepo;
        $this->assetRepo = $assetRepo;        
    }
    
    /**
     * 
     * @param UploadedFileInterface $uploadedFile
     * @param type $editedItemId
     * @param type $editor
     * @return string Location
     */
    public function storeAsset(UploadedFileInterface $uploadedFile, $editedItemId, $editor) {
        // relativní cesta vzhledem k rootu
        $baseFilepath = ConfigurationCache::redUploads()['upload.red'];
        $clientFileName = urldecode($uploadedFile->getClientFilename());  // někdy - např po ImageTools editaci je název souboru z Tiny url kódován
        $clientMime = $uploadedFile->getClientMediaType();
        $targetFilepath = $this->prepareAssetFilePath($baseFilepath, $clientFileName);
        $uploadedFile->moveTo($targetFilepath);
        $this->recordAsset($clientFileName, $clientMime, $editedItemId, $editor);  
        return $targetFilepath;
    } 
    
    private function prepareAssetFilePath($baseFilepath, $clientFileName) {
        return $baseFilepath.'/'.$clientFileName;        
    }
    
    private function recordAsset($clientFileName, $clientMime, $editedItemId, $editor) {
        $asset = $this->assetRepo->findByFilename($clientFileName);
        if ($asset) { //array
            // mám asset v databázi
            if ($this->menuItemAssetRepo->get($editedItemId, $asset->getId())) {
                // asset byl již dříve uložen pro aktuální item
            } else {
                $menuItemIds = $this->menuItemAssetRepo->findByAssetId($asset->getId());
                if ($menuItemIds) {  // array
                    // asset byl již uložen pro jiný (jiné) menu item
                    $this->addMenuitemAsset($editedItemId, $asset);
                } else {
                    throw new RuntimeException(" V databázi nalezen asset '$clientFileName', který není uložen jako asset pro menu item s id '$editedItemId'.");
                }
            }
        } else {
            $this->addAsset($clientFileName, $clientMime, $editedItemId, $editor);
        }
        
    }
    
    private function addAsset($clientFileName, $clientMime, $editedItemId, $editor) {
        $asset = new Asset();
        $asset->setFilepath($clientFileName);
        $asset->setMimeType($clientMime);
        $asset->setEditorLoginName($editor);
        $this->assetRepo->add($asset);
        $this->assetRepo->flush();
        $this->addMenuitemAsset($editedItemId, $asset);
    }
    
    private function addMenuitemAsset($editedItemId, AssetInterface $asset) {
        $menuiteAsset = new MenuItemAsset();
        $menuiteAsset->setMenuItemIdFk($editedItemId); 
        $menuiteAsset->setAssetIdFk($asset->getId());
        $this->menuItemAssetRepo->add($menuiteAsset);
    }
}
