<?php
namespace Red\Service\Menu;

use Component\ViewModel\StatusViewModelInterface;

use Red\Model\Repository\MenuItemRepoInterface;
use Red\Model\Repository\MenuRootRepo;
use Red\Model\Dao\Hierarchy\HierarchyDaoInterface;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class Menu {
    
    private $statusViewModel;


    private $menuItemRepo;
    private $menuRootRepo;
    private $hierarchyDao;
    private $menuConfigs;

    public function __construct(
            StatusViewModelInterface $statusViewModel,
            MenuItemRepoInterface $menuItemRepo,
            MenuRootRepo $menuRootRepo,
            HierarchyDaoInterface $hierarchyDao,
            $menuConfigs  // $this->container->get('menu.services');
            ) {
        $this->statusViewModel = $statusViewModel;
        $this->menuItemRepo = $menuItemRepo;
        $this->menuRootRepo = $menuRootRepo;
        $this->menuConfigs = $menuConfigs;
    }
    
    
    
    public function getMenuItem($uid) {
        return $this->menuItemRepo->get($this->statusViewModel->getPresentedLanguage()->getLangCode(), $uid);        
    }
    
    public function getItemType($uid) {
        $hierarchyRow = $this->hierarchyDao->get(['uid'=>$uid]);
        
        foreach ($this->menuConfigs as $menuConfig) {
            $menuRoot = $this->menuRootRepo->get($menuConfig['rootName']);
            /** @var MenuRootInterface $menuRoot */
            $menurootHierarchyRow = $this->hierarchyDao->get(['uid'=>$menuRoot->getUidFk()]);
            if ($menurootHierarchyRow['left_node']<$hierarchyRow['left_node'] AND $menurootHierarchyRow['right_node']>$hierarchyRow['right_node']) {
                $itemType = $menuConfig['itemtype'];
                break;
            }
        }
        return $itemType;
    }
        
    public function presentEditableMenu(): bool {
        return $this->statusViewModel->presentEditableMenu();
    }
    
    public function isPasteMode(): bool {
        $cut = $this->statusViewModel->getFlashPostCommand(HierarchyControler::POST_COMMAND_CUT);
        $copy = $this->statusViewModel->getFlashPostCommand(HierarchyControler::POST_COMMAND_COPY);
        return ($cut OR $copy);        
    }
    
    public function createDriverComponent($uid) {
        /** @var DriverComponent $driver */
        $driver = $this->container->get(DriverComponent::class);
        /** @var DriverViewModelInterface $driverViewModel */
        $driverViewModel = $driver->getData();
        // před přidáním DriverButtonsComponent a button komponentů musí DriverViewModel mít nastaven menuItem a itemType
        $menuItem = $this->getMenuItem($uid);
        
        $presentedItem = $this->statusViewModel->getPresentedMenuItem();
        $isPresented = isset($presentedItem) && ($presentedItem->getId() == $menuItem->getId());
        $driverViewModel->setPresented($isPresented);
        if($this->presentEditableMenu()) {   // renderery!! -> nemusíš znát presented v rendereru a pak tady nemusíš znát driverViewModel
            $driver->setRendererName(DriverRendererEditable::class);
            $this->appendDriverButtonsComponent($driver, $this->isPasteMode(), $this->getItemType($uid));
            $this->appendDriverButtonsComponent($driver, $this->isPasteMode(), $this->getItemType($uid));
        } else {
            $driver->setRendererName(DriverRenderer::class);                                    
        }        
        return $driver;
    }    
}
