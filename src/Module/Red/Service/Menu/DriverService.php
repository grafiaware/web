<?php
namespace Red\Service\Menu;

use Component\ViewModel\StatusViewModelInterface;

use Red\Model\Repository\MenuItemRepoInterface;
use Red\Model\Repository\MenuRootRepo;
use Red\Model\Dao\Hierarchy\HierarchyDaoInterface;

use Red\Component\View\Menu\DriverComponentInterface;
use Red\Component\View\Menu\DriverButtonsComponent;

use Red\Component\ViewModel\Menu\Enum\ItemTypeEnum;

use Red\Component\Renderer\Html\Menu\DriverRenderer;
use Red\Component\Renderer\Html\Menu\DriverRendererEditable;
use Red\Component\Renderer\Html\Menu\DriverPresentedRenderer;
use Red\Component\Renderer\Html\Menu\DriverPresentedRendererEditable;

use Red\Component\View\Manage\ButtonsMenuItemManipulationComponent;
use Red\Component\View\Manage\ButtonsMenuAddComponent;
use Red\Component\View\Manage\ButtonsMenuAddOnelevelComponent;
use Red\Component\View\Manage\ButtonsMenuCutCopyComponent;
use Red\Component\View\Manage\ButtonsMenuCutCopyEscapeComponent;
use Red\Component\View\Manage\ButtonsMenuDeleteComponent;

use Red\Component\Renderer\Html\Manage\ButtonsItemManipulationRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuAddMultilevelRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuAddOnelevelRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuPasteOnelevelRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuPasteMultilevelRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuCutCopyRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuCutCopyEscapeRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuDeleteRenderer;

use Red\Middleware\Redactor\Controler\HierarchyControler;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class DriverService implements DriverServiceInterface{
    
    private $statusViewModel;


    private $menuItemRepo;
    private $menuRootRepo;
    private $hierarchyDao;
    private $menuConfigs;
    private $container;

    public function __construct(
            StatusViewModelInterface $statusViewModel,
            MenuItemRepoInterface $menuItemRepo,
            MenuRootRepo $menuRootRepo,
            HierarchyDaoInterface $hierarchyDao,
            $menuConfigs,
            $container
            ) {
        $this->statusViewModel = $statusViewModel;
        $this->menuItemRepo = $menuItemRepo;
        $this->menuRootRepo = $menuRootRepo;
        $this->hierarchyDao = $hierarchyDao;
        $this->menuConfigs = $menuConfigs;
        $this->container = $container;      //TODO: ODSTRANIT! kontejner
    }
    
    
    
    private function getMenuItem($uid) {
        return $this->menuItemRepo->get($this->statusViewModel->getPresentedLanguage()->getLangCode(), $uid);        
    }
    
    private function getItemType($uid) {
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
        
    private function presentEditableMenu(): bool {
        return $this->statusViewModel->presentEditableMenu();
    }
    
    private function isPasteMode(): bool {
        $cut = $this->statusViewModel->getFlashPostCommand(HierarchyControler::POST_COMMAND_CUT);
        $copy = $this->statusViewModel->getFlashPostCommand(HierarchyControler::POST_COMMAND_COPY);
        return ($cut OR $copy);        
    }


    ### buttons ###

    private function appendDriverButtonsComponent(DriverComponentInterface $driver, $pasteMode, $itemType): DriverButtonsComponent {
               $setRenderingByAccess = function (ViewInterface $component, $rendererName) {
                    /** @var AccessPresentationInterface $accessPresentation */
                    $accessPresentation = $this->container->get(AccessPresentation::class);
                    if($accessPresentation->isAllowed(get_class($component), AccessPresentationEnum::EDIT)) {
                        $component->setRendererName($rendererName);
                    } else {
                        $component->setRendererName(NoPermittedContentRenderer::class);
                    }
                    $component->setRendererContainer($this->rendererContainer);
                    return $component;
                }; 
                $createItemManipulationButtons = function ($setRenderingByAccess) {
                    return $setRenderingByAccess(new ButtonsMenuItemManipulationComponent($this->configuration), ButtonsItemManipulationRenderer::class);
                };
                $createDeleteButtons = function ($setRenderingByAccess) {
                    return $setRenderingByAccess(new ButtonsMenuDeleteComponent($this->configuration), ButtonsMenuDeleteRenderer::class);
                };
                $createCutCopyButtons = function ($setRenderingByAccess, $pasteMode) {
                    if ($pasteMode) {
                        return $setRenderingByAccess(new ButtonsMenuCutCopyComponent($this->configuration), ButtonsMenuCutCopyEscapeRenderer::class);
                    } else {
                        return $setRenderingByAccess(new ButtonsMenuCutCopyComponent($this->configuration), ButtonsMenuCutCopyRenderer::class);
                    }
                };
                $createAddOrPasteOnelevelButtons = function ($setRenderingByAccess, $pasteMode) {
                    if ($pasteMode) {
                        return $setRenderingByAccess(new ButtonsMenuAddComponent($this->configuration), ButtonsMenuPasteOnelevelRenderer::class);
                    } else {
                        return $setRenderingByAccess(new ButtonsMenuAddComponent($this->configuration), ButtonsMenuAddOnelevelRenderer::class);
                    }
                };
                $createAddOrPasteMultilevelButtons = function ($setRenderingByAccess, $pasteMode) {
                    if ($pasteMode) {
                        return $setRenderingByAccess(new ButtonsMenuAddComponent($this->configuration), ButtonsMenuPasteMultilevelRenderer::class);
                    } else {
                        return $setRenderingByAccess(new ButtonsMenuAddComponent($this->configuration), ButtonsMenuAddMultilevelRenderer::class);
                    }
                };
                ####         
        
        $driverButtons = $this->container->get(DriverButtonsComponent::class);
        $driver->appendComponentView($driverButtons, DriverComponentInterface::DRIVER_BUTTONS);// DriverButtonsComponent je typu InheritData - tímto vložením dědí DriverViewModel
        #### buttons ####
        $buttonComponents = [];
        switch ($itemType) {
            // ButtonsXXX komponenty jsou typu InheritData - dědí DriverViewModel
            case ItemTypeEnum::MULTILEVEL:
                $buttonComponents[] = $createItemManipulationButtons($setRenderingByAccess);
                $buttonComponents[] = $createAddOrPasteMultilevelButtons($setRenderingByAccess, $pasteMode);
                $buttonComponents[] = $createCutCopyButtons($setRenderingByAccess, $pasteMode);
                break;
            case ItemTypeEnum::ONELEVEL:
                $buttonComponents[] = $createItemManipulationButtons($setRenderingByAccess);
                $buttonComponents[] = $createAddOrPasteOnelevelButtons($setRenderingByAccess, $pasteMode);
                $buttonComponents[] = $createCutCopyButtons($setRenderingByAccess, $pasteMode);
                break;
            case ItemTypeEnum::TRASH:
                $buttonComponents[] = $createCutCopyButtons($setRenderingByAccess, $pasteMode);
                $buttonComponents[] = $createDeleteButtons($setRenderingByAccess);
                break;
            default:
                throw new LogicException("Nerozpoznán typ položek menu (hodnota vrácená metodou viewModel->getItemType())");
        }
        $driverButtons->appendComponentViewCollection($buttonComponents);  // tady button komponenty dědí DriverViewModel
        return $driverButtons;
    }
    
    public function completeDriverComponent(DriverComponentInterface $driver, $uid){
        $menuItem = $this->getMenuItem($uid);
        $driver->getData()->withMenuItem($menuItem);
        $presentedItem = $this->statusViewModel->getPresentedMenuItem();
        $isPresented = isset($presentedItem) && ($presentedItem->getId() == $menuItem->getId());
        if($this->presentEditableMenu()) {   // renderery!! -> nemusíš znát presented v rendereru a pak tady nemusíš znát driverViewModel
            if($isPresented) {
                $driver->setRendererName(DriverPresentedRendererEditable::class);
//                $this->appendDriverButtonsComponent($driver, $this->isPasteMode(), $this->getItemType($uid));
            } else {
                $driver->setRendererName(DriverRendererEditable::class);
            }
        } else {
            if($isPresented) {
                $driver->setRendererName(DriverPresentedRenderer::class);                                    
            } else {
                $driver->setRendererName(DriverRenderer::class);                                    
            }
        }        
        return $driver;
    }    
}
