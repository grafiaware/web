<?php
namespace Red\Service\Asset;

use Psr\Http\Message\UploadedFileInterface;
use Site\ConfigurationCache;
use Pes\Core\Directory\Directory;

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
    

    #[\Override]
    public function storeAsset(UploadedFileInterface $uploadedFile, string $editedItemId, string $editor) {

        $clientFileName = urldecode($uploadedFile->getClientFilename());  // někdy - např po ImageTools editaci je název souboru z Tiny url kódován
        $clientMime = $uploadedFile->getClientMediaType();
        $targetFilepath = $this->prepareAssetTargetFilePath($clientFileName);
        
        $uploadedFile->moveTo($targetFilepath);
        $this->recordAsset($clientFileName, $clientMime, $editedItemId, $editor);  
        
        return $targetFilepath;
    } 
    
    /**
     * Připraví cílovou cestu v uložení uploadovaného souboru - ve tvaru vhodném jako odkaz do html - relativní cesta vzhledem k rootu (t.j. např. hodnota pro <img src="cesta")
     * 
     * @param type $clientFileName
     * @return type
     */
    private function prepareAssetTargetFilePath($clientFileName) {
        // relativní cesta vzhledem k rootu
        $baseFilepath = ConfigurationCache::redUploads()['upload.red'];
        return $baseFilepath.'/'.$clientFileName;        
    }
    
    private function recordAsset($clientFileName, $clientMime, $editedItemId, $editor) {
        $assetsWithFilename = $this->assetRepo->findByFilename($clientFileName);
        foreach ($assetsWithFilename as $asset) {
            // mám asset v databázi
            if ($this->menuItemAssetRepo->get($editedItemId, $asset->getId())) {
                // asset byl již dříve uložen pro aktuální item
            } else {
                $menuItemAssets = $this->menuItemAssetRepo->findByAssetId($asset->getId());
                foreach ($menuItemAssets as $menuItemAsset) {
                    // asset byl již uložen pro jiný (jiné) menu item
                    $this->addMenuitemAsset($editedItemId, $asset);
                }
//                else {
//                    throw new RuntimeException(" V databázi nalezen asset '$clientFileName', který není uložen jako asset pro menu item s id '$editedItemId'.");
//                }
            }
// else {
//            $this->addAsset($clientFileName, $clientMime, $editedItemId, $editor);
//        }            
        }
        
    }
    
    private function addAsset($clientFileName, $clientMime, $editedItemId, $editor) {
        $asset = new Asset();
        $asset->setFilepath($clientFileName);
        $asset->setMimeType($clientMime);
        $asset->setEditorLoginName($editor);
        $this->assetRepo->add($asset);
//        $this->assetRepo->flush();  // neměl by být potřebná AssetDao je DaoEditAutoincrementKeyInterface
        $this->addMenuitemAsset($editedItemId, $asset);
    }
    
    private function addMenuitemAsset($editedItemId, AssetInterface $asset) {
        $menuiteAsset = new MenuItemAsset();
        $menuiteAsset->setMenuItemIdFk($editedItemId); 
        $menuiteAsset->setAssetIdFk($asset->getId());
        $this->menuItemAssetRepo->add($menuiteAsset);
    }
}
