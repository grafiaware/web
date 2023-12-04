<?php
namespace Red\Service\ItemApi;

use Pes\View\ViewInterface;
use Red\Model\Entity\MenuItemInterface;
use Red\Service\ItemCreator\Enum\ApiGeneratorEnum;
use Pes\Text\FriendlyUrl;

use UnexpectedValueException;

/**
 * Description of ItemLoader
 *
 * @author pes2704
 */
class ItemApiService implements ItemApiServiceInterface {
    
    const DEFAULT_MODULE = 'red';
    const DEFAULT_GENERATOR = 'empty';
    
    /**
     * 
     * @param MenuItemInterface $menuItem
     * @return string
     * @throws UnexpectedValueException
     */
    public function getLoaderApiUri(MenuItemInterface $menuItem) {
        $apiModule = $menuItem->getApiModuleFk();
        $apiGenerator = $menuItem->getApiGeneratorFk();
        if (!isset($apiModule)){
            if (!isset($apiGenerator)) {
                throw new UnexpectedValueException("Nelze vytvořit content loader api uri pro menu item s id '{$menuItem->getId()}'. Menu item nemá nastaven api module a api generator.");
            } else {
                $apiModule = self::DEFAULT_MODULE;
            }
        } elseif (!isset($apiGenerator)) {
            throw new UnexpectedValueException("Nelze vytvořit content loader api uri pro menu item s id '{$menuItem->getId()}'. Menu item nemá nastaven api generator.");
        }
        if($apiGenerator==ApiGeneratorEnum::STATIC_GENERATOR) {
            $id = $this->getNameForStaticPage($menuItem);
        } else {
            $id = $menuItem->getId();
        }
        return "$apiModule/v1/$apiGenerator/$id";
    }
    
    private function getNameForStaticPage(MenuItemInterface $menuItem) {
        $menuItemPrettyUri = $menuItem->getPrettyuri();
        if (isset($menuItemPrettyUri) AND $menuItemPrettyUri AND strpos($menuItemPrettyUri, "folded:")===0) {      // EditItemController - line 93
            $name = str_replace('/', '_', str_replace("folded:", "", $menuItemPrettyUri));  // zahodí prefix a nahradí '/' za '_' - recipročně
        } else {
            $name = FriendlyUrl::friendlyUrlText($menuItem->getTitle());
        }
        return $name;
    }
}
