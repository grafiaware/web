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
    
    const EMPTY_MODULE = 'red';
    const EMPTY_GENERATOR = 'empty';
    
    /**
     * 
     * @param MenuItemInterface $menuItem
     * @return string
     * @throws UnexpectedValueException
     */
    public function getLoaderApiUri(MenuItemInterface $menuItem) {
        $apiModule = $menuItem->getApiModuleFk();
        $apiGenerator = $menuItem->getApiGeneratorFk();
        if (!isset($apiModule) AND !isset($apiGenerator)) {
            $apiModule = self::EMPTY_MODULE;
            $apiGenerator = self::EMPTY_GENERATOR;
        } elseif (!isset($apiModule) AND !isset($apiGenerator)) {
            throw new UnexpectedValueException("Nelze vytvořit content loader api uri pro menu item s id '{$menuItem->getId()}'. Menu item musí mít hodnoty api module a api generator obě nastaveny nebo obě NULL.");
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
