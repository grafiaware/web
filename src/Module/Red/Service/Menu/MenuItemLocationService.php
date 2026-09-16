<?php
namespace Red\Service\Menu;

use Red\Model\Dao\Hierarchy\HierarchyDaoInterface;
use Red\Model\Repository\MenuRootRepo;
use Red\Middleware\Redactor\Controler\HierarchyControler;

/**
 * Nested-set příslušnost položky k menu trash (kořen menu_root.name = trash).
 */
class MenuItemLocationService implements MenuItemLocationServiceInterface {

    private MenuRootRepo $menuRootRepo;
    private HierarchyDaoInterface $hierarchyDao;

    public function __construct(MenuRootRepo $menuRootRepo, HierarchyDaoInterface $hierarchyDao) {
        $this->menuRootRepo = $menuRootRepo;
        $this->hierarchyDao = $hierarchyDao;
    }

    public function isInTrash(string $uid): bool {
        $trashRoot = $this->menuRootRepo->get(HierarchyControler::TRASH_MENU_ROOT);
        if (!$trashRoot) {
            return false;
        }
        $itemRow = $this->hierarchyDao->get(['uid' => $uid]);
        $trashRow = $this->hierarchyDao->get(['uid' => $trashRoot->getUidFk()]);
        if (!$itemRow || !$trashRow) {
            return false;
        }
        return $trashRow['left_node'] <= $itemRow['left_node']
            && $trashRow['right_node'] >= $itemRow['right_node'];
    }
}
