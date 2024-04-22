<?php

namespace Red\Component\View\Menu;

use Pes\View\CollectionViewInterface;
use Psr\Container\ContainerInterface;

use Component\View\ComponentCompositeAbstract;

use Configuration\ComponentConfigurationInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Component\ViewModel\Menu\MenuViewModelInterface;
use Red\Component\ViewModel\Menu\LevelViewModelInterface;
use Red\Component\ViewModel\Menu\ItemViewModelInterface;
use Red\Component\ViewModel\Menu\ItemViewModel;
use Red\Component\ViewModel\Menu\DriverViewModelInterface;
use Red\Component\ViewModel\Menu\DriverViewModel;

use Red\Component\View\Menu\LevelComponent;
use Red\Component\View\Menu\LevelComponentInterface;
use Red\Component\View\Menu\ItemComponent;
use Red\Component\View\Menu\ItemComponentInterface;
use Red\Component\View\Menu\DriverComponent;
use Red\Component\View\Menu\DriverComponentInterface;
use Red\Component\View\Menu\DriverButtonsComponent;

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

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;
// pro metodu -> do contejneru
use Access\AccessPresentation;
use Pes\View\ViewInterface;
use Component\Renderer\Html\NoPermittedContentRenderer;
//TODO: co s tímto fantem DriverRendererEditable
use Red\Component\Renderer\Html\Menu\DriverRenderer,    Red\Component\Renderer\Html\Menu\DriverRendererEditable;

use Red\Component\ViewModel\Menu\Enum\ItemTypeEnum;

use LogicException;

/**
 * Description of MenuComponent
 *
 * @author pes2704
 */
class MenuComponent extends ComponentCompositeAbstract implements MenuComponentInterface {

    private $container;

    /**
     * @var MenuViewModelInterface
     */
    protected $contextData;

    private $levelRendererName;
    
    private $rootRealDepth;

    public function __construct(ComponentConfigurationInterface $configuration, ContainerInterface $container) {
        parent::__construct($configuration);
        $this->container = $container;
    }
    
    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::VISITOR => [AccessPresentationEnum::DISPLAY => static::class]
        ];
    }
    
    public function getString() {
        $str = parent::getString();
        return $str;
    }

    /**
     *
     * @param $levelRendererName
     * @return MenuComponentInterface
     */
    public function setRenderersNames($levelRendererName): MenuComponentInterface {
        $this->levelRendererName = $levelRendererName;
        return $this;
    }

    /**
     * Připraví MenuComponent a strom všech jejích potomků k renderování
     * 
     * @return void
     * @throws \LogicException
     */
    public function beforeRenderingHook(): void {
        $subtreeNodeModels = $this->contextData->getNodeModels();
        $topLevelComponent = $this->buildMenuComponentsTree($subtreeNodeModels);
        $this->appendComponentView($topLevelComponent, MenuComponentInterface::MENU);        
    }

    /**
     * Z pole dvojic ItemModel+DriverModel vygeneruje "strom" komponentů ve struktuře:
     * # Level komponenta obsahuje Item komponenty 
     * # každá Item komponenta obsahuje Driver komponetu a pokud menu má další úroveň, obsahuje i Level koponentu
     * rekurzivně každá Level komponenta obsahuje Item komponenty
     * 
     *  - Level
     *      - Item
     *          - Driver
     *      - Item
     *          - Driver
     *          - Level
     *              - Item
     *                  - Driver
     *              - Item
     *                  - Driver
     * 
     * Komponentám nastaví data a jména rendererů. Jména rendererů vybírá z jmen nastaveným metodou $this->setRenderersNames(...).
     * 
     * Výsledkem je Level komponenta s kompozitními komponentami (view) připravená na renderování.
     * 
     * @param array $subtreeNodeModels
     * @return LevelComponentInterface
     */
    private function buildMenuComponentsTree(array $subtreeNodeModels): LevelComponentInterface {
        // minimální hloubka u menu bez zobrazení kořenového prvku je 2 (pro 1 je nodes pole v modelu prázdné), 
        // u menu se zobrazením kořenového prvku je minimálmí hloubka 1, ale $subtreeNodeModels pak obsahuje jen kořenový prvek
        $level = '';
        $itemTags = [];
        $itemComponentStack = [];
        $first = true;
        foreach ($subtreeNodeModels as $treeNodeModel) {
            /** @var ItemViewModelInterface $treeNodeModel */
            $itemDepth = $treeNodeModel['realDepth'];
            if ($first) {
                $this->rootRealDepth = $itemDepth;
                $currDepth = $itemDepth;
                $first = false;
            }
            if ($itemDepth>$currDepth) {
                $itemComponentStack[$itemDepth][] = $this->newNodeComponents($treeNodeModel);
                $currDepth = $itemDepth;
            } elseif ($itemDepth<$currDepth) {
                $this->createChildrenComponents($currDepth, $itemDepth, $itemComponentStack);
                $itemComponentStack[$itemDepth][] = $this->newNodeComponents($treeNodeModel);
                $currDepth = $itemDepth;
            } else {
                $itemComponentStack[$currDepth][] = $this->newNodeComponents($treeNodeModel);
            }
        }
        return $this->createLevelComponent($this->rootRealDepth, $itemComponentStack[$this->rootRealDepth]);
    }
    
    private function newNodeComponents($treeNodeModel) {
        $item = $this->newItemComponent($treeNodeModel);
        $driver = $this->newDriverComponent($treeNodeModel['menuItem']);
        $item->appendComponentView($driver, ItemComponentInterface::DRIVER);
        return $item;
    }  
    
    /**
     * Nový ItemComponent
     * 
     * @param ItemViewModelInterface $itemViewModel
     * @return ItemComponent
     */
    private function newItemComponent($treeNodeModel) {
        /** @var ItemComponent $item */
        $item = $this->container->get(ItemComponent::class);
        $itemViewModel = $item->getData();
        $itemViewModel->setRealDepth($treeNodeModel['realDepth']);
        $itemViewModel->setOnPath($treeNodeModel['isOnPath']);
        $itemViewModel->setLeaf($treeNodeModel['isLeaf']);
        
        return $item;
    }
    
    /**
     * Nový DriverComponent
     * 
     * @param MenuItemInterface $menuItem
     * @param type $editableMode
     * @return DriverComponent
     */
    private function newDriverComponent(MenuItemInterface $menuItem) {
        return $this->createDriverComponent($menuItem, $this->contextData->getItemType());
    }
    
    /**
     * Nový DriverComponent
     * 
     * @param MenuItemInterface $menuItem
     * @param type $editableMode
     * @return DriverComponent
     */
    public function createDriverComponent(MenuItemInterface $menuItem, $itemType) {   // PUBLIC pro volání z ComponentControler

        /** @var DriverComponent $driver */
        $driver = $this->container->get(DriverComponent::class);
        /** @var DriverViewModelInterface $driverViewModel */
        $driverViewModel = $driver->getData();
        // před přidáním DriverButtonsComponent a button komponentů musí DriverViewModel mít nastaven menuItem a itemType
        $driverViewModel->withMenuItem($menuItem);
        $driverViewModel->setItemType($itemType);
        $presentedItem = $this->contextData->getPresentedMenuItem();
        $isPresented = isset($presentedItem) && ($presentedItem->getId() == $menuItem->getId());
        $driverViewModel->setPresented($isPresented);
        if($driverViewModel->presentEditableMenu()) {
            $driver->setRendererName(DriverRendererEditable::class);
            $this->appendDriverButtonsComponent($driver, $driverViewModel->isPasteMode(), $driverViewModel->getItemType());
        } else {
            $driver->setRendererName(DriverRenderer::class);                                    
        }
        return $driver;
    }
    
    /**
     * Vytvoří LevelComponent připojí mu kolekci vnořených ItemComponent. Vytvořený LevelComponent přidá do nadřazeného ItemComponent.
     * 
     * @param type $currDepth
     * @param type $targetDepth
     * @param type $itemComponentStack
     */
    private function createChildrenComponents($currDepth, $targetDepth, &$itemComponentStack) {
        for ($i=$currDepth; $i>$targetDepth; $i--) {
            $levelComponent = $this->createLevelComponent($targetDepth, $itemComponentStack[$i]);
            unset($itemComponentStack[$i]);
            if (isset($itemComponentStack[$i-1])) {
                $parentItemComponent = end($itemComponentStack[$i-1]);
                $parentItemComponent->appendComponentView($levelComponent, ItemComponentInterface::LEVEL);
            }
        }
    }

    /**
     * Vytvoří LevelComponent a připojí mu kolekci vnořených ItemComponent
     * 
     * @param type $targetDepth
     * @param type $itemComponents
     * @return LevelComponentInterface
     */
    private function createLevelComponent($targetDepth, $itemComponents): LevelComponentInterface {
        /** @var LevelComponentInterface $levelComponent */
        $levelComponent = $this->container->get(LevelComponent::class);
        $levelViewModel = $levelComponent->getData();
        /** @var LevelViewModelInterface $levelViewModel */
        $levelViewModel->setLastLevel($targetDepth==$this->rootRealDepth);
        $levelComponent->setRendererName($this->levelRendererName);
        $levelComponent->appendComponentViewCollection($itemComponents);
        return $levelComponent;
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
}
