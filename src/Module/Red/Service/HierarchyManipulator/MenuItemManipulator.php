<?php
namespace Red\Service\HierarchyManipulator;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Service\HierarchyManipulator\MenuItemToggleResultEnum;

/**
 * Description of MenuItemxManipulator
 *
 * @author pes2704
 */
class MenuItemManipulator implements MenuItemManipulatorInterface {

    /**
     * @var MenuItemRepo
     */
    private $menuItemRepo;

    /**
     * @var HierarchyAggregateReadonlyDao
     */
    private $hierarchyDao;


    public function __construct(
            MenuItemRepo $menuItemRepo,
            HierarchyAggregateReadonlyDao $hierarchyDao
            ) {
        $this->menuItemRepo = $menuItemRepo;
        $this->hierarchyDao = $hierarchyDao;
    }

    /**
     * Přepne položku pasivní (nepublikovanou) na aktivní (publikovanou) a naopak.
     *
     * Pozor! Nesmí nastat situace kdy pasivní položka menu má aktivní potomky - to by způsobilo chybné načítání celé
     * struktury menu v needitačním režimu (mimo redakčního systému) - načítají se pouze aktivní položky a pokud potomkem neaktivní položky by byla aktivní položka, v načteném
     * stromu by byly "díry".
     *
     * - Pokud je položka aktivní přepne na pasivní i všechny její potomky
     * - Pokud je položka pasivní zjití jestli její rodič je aktivní, pokud rodič je aktivní přepne tuto položku na aktivní (potomky nemění),
     * - Pokud je rodič pasivní nemění nic (ponechá položku pasivní).
     *
     * @param string $langCode
     * @param string $uid
     * @return string Návratová hodnota je hodnotou jedné z konstant v MenuItemToggleResultEnum
     */
    public function toggleItems($langCode, $uid) {
        $menuItem = $this->menuItemRepo->get($langCode, $uid);
        $active = $menuItem->getActive();
        if ($active) {
            // metoda se volá jen v editačním režimu -> načtou se i neaktivní (nepulikované) položky
            $subNodes = $this->hierarchyDao->getSubTree($langCode, $uid);  // včetně "kořene"
            foreach ($subNodes as $node) {
                $menuItem = $this->menuItemRepo->get($langCode, $node['uid']);
                if (isset($menuItem)) {
                    $menuItem->setActive(0);  //active je integer
                }
            }
            if (count($subNodes)>1) {
                $msg = MenuItemToggleResultEnum::DEACTOVATE_WITH_DESCENDANTS;
            } else {
                $msg = MenuItemToggleResultEnum::DEACTOVATE_ONE;
            }
        } else {
            $parent = $this->hierarchyDao->getParent($langCode, $uid);
            $parentMenuItem = $this->menuItemRepo->get($langCode, $parent['uid']);
            if (isset($parentMenuItem)AND $parentMenuItem->getActive()) {
                $menuItem->setActive(1);  //active je integer
                $msg = MenuItemToggleResultEnum::ACTIVATE_ONE;
            } else {
                $msg = MenuItemToggleResultEnum::UNABLE_ACTIVATE;
            }
        }
        return $msg;
    }


}
