<?php
namespace Red\Service\MenuItemxManipulator;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;

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
                $msg = MenuItemManipulatorInterface::DEACTOVATE_WITH_DESCENDANTS;
            } else {
                $msg = MenuItemManipulatorInterface::DEACTOVATE_ONE;
            }
        } else {
            $parent = $this->hierarchyDao->getParent($langCode, $uid);
            $parentMenuItem = $this->menuItemRepo->get($langCode, $parent['uid']);
            if (isset($parentMenuItem)AND $parentMenuItem->getActive()) {
                $menuItem->setActive(1);  //active je integer
                $msg = MenuItemManipulatorInterface::ACTIVATE_ONE;
            } else {
                $msg = MenuItemManipulatorInterface::UNABLE_ACTIVATE;
            }
        }
        return $msg;
    }




}
