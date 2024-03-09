<?php

namespace Red\Component\View\Menu;

use Pes\View\CollectionViewInterface;
use Psr\Container\ContainerInterface;

use Component\View\ComponentCompositeAbstract;

use Configuration\ComponentConfigurationInterface;

use Red\Component\ViewModel\Menu\MenuViewModelInterface;
use Red\Component\ViewModel\Menu\LevelViewModelInterface;
use Red\Component\ViewModel\Menu\ItemViewModelInterface;
use Red\Component\ViewModel\Menu\DriverViewModelInterface;

use Red\Component\View\Menu\LevelComponent;
use Red\Component\View\Menu\LevelComponentInterface;
use Red\Component\View\Menu\ItemButtonsComponent;
use Red\Component\View\Menu\ItemComponent;
use Red\Component\View\Menu\ItemComponentInterface;
use Red\Component\View\Menu\DriverComponent;
use Red\Component\View\Menu\DriverComponentInterface;

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

use Red\Component\ViewModel\Menu\Enum\ItemRenderTypeEnum;

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
    private $itemRendererName;
    private $itemEditableRendererName;
    private $driverRendererName;
    private $driverEditableRendererName;
    
    private $rootRealDepth;
    private $editableMode;

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
    public function setRenderersNames($levelRendererName, $itemRendererName, $itemEditableRendererName, $driverRendererName, $driverEditableRendererName): MenuComponentInterface {
        $this->levelRendererName = $levelRendererName;
        $this->itemRendererName = $itemRendererName;
        $this->itemEditableRendererName = $itemEditableRendererName;
        $this->driverRendererName = $driverRendererName;
        $this->driverEditableRendererName = $driverEditableRendererName;
        return $this;
    }

    /**
     * Z pole ItemModelů vygeneruje "strom" komponentů - menu komponentě přidá kompozitní level komponenty, předtím jednotlivým level komponentám přidá komponentní item komponenty,
     * atd.
     * Výsledkem je tato komponenta s kompozitními komponentami (view) připravená na renderování.
     *
     * @return void
     * @throws \LogicException
     */
    public function beforeRenderingHook(): void {

        // minimální hloubka u menu bez zobrazení kořenového prvku je 2 (pro 1 je nodes pole v modelu prázdné), u menu se zobrazením kořenového prvku je minimálmí hloubka 1, ale nodes pak obsahuje jen kořenový prvek
        $this->editableMode = $this->contextData->presentEditableMenu();

        $subtreeNodeModels = $this->contextData->getNodeModels();

        if ($subtreeNodeModels) {
            $level = '';
            $itemTags = [];
            $itemComponentStack = [];
            $first = true;
            foreach ($subtreeNodeModels as $itemNodeModel) {
                /** @var ItemViewModelInterface $itemNodeModel */
                $itemDepth = $itemNodeModel[0]->getRealDepth();
                if ($first) {
                    $this->rootRealDepth = $itemDepth;
                    $currDepth = $itemDepth;
                    $first = false;
                }
                if ($itemDepth>$currDepth) {
                    $itemComponentStack[$itemDepth][] = $this->newItemComponent($itemNodeModel);
                    $currDepth = $itemDepth;
                } elseif ($itemDepth<$currDepth) {
                    $this->createChildrenComponents($currDepth, $itemDepth, $itemComponentStack);
                    $itemComponentStack[$itemDepth][] = $this->newItemComponent($itemNodeModel);
                    $currDepth = $itemDepth;
                } else {
                    $itemComponentStack[$currDepth][] = $this->newItemComponent($itemNodeModel);
                }
            }
            $this->createChildrenComponents($currDepth, $this->rootRealDepth, $itemComponentStack);
            $this->createLastLevelChildrenComponents($itemComponentStack);
        }
    }

    private function newItemComponent($itemNodeModel) {
        /** @var ItemViewModelInterface $itemViewModel */
        $itemViewModel = $itemNodeModel[0];
        /** @var DriverViewModelInterface $driverViewModel */
        $driverViewModel = $itemNodeModel[1];
        
        /** @var ItemComponent $item */
        $item = new ItemComponent($this->configuration);
        $item->setData($itemViewModel)->setRendererContainer($this->rendererContainer);
        /** @var DriverComponent $driver */
        $driver = new DriverComponent($this->configuration);
        $driver->setData($driverViewModel)->setRendererContainer($this->rendererContainer); 
        $item->appendComponentView($driver, ItemComponentInterface::DRIVER);
        if($this->editableMode) {
            $item->setRendererName($this->itemEditableRendererName);
            $driver->setRendererName($this->driverEditableRendererName);

            // u kořenového item menu nejsou buttony
            if ($driverViewModel->isPresented() AND !$itemViewModel->isRoot()) {
                $itemButtons = $this->createItemButtonsComponent();
                $item->appendComponentView($itemButtons, DriverComponentInterface::ITEM_BUTTONS);
            }
        } else {
            $item->setRendererName($this->itemRendererName);
            $driver->setRendererName($this->driverRendererName);
        }        
        
       
        
        return $item;
    }
    
    
    /**
     * Vytvoří LevelComponent připojí mu kolekci vnořených ItemComponent. Vytvořený LevelComponent "odloží" do nadřazeného ItemviewModelu.
     * 
     * @param type $currDepth
     * @param type $targetDepth
     * @param type $itemComponentStack
     */
    private function createChildrenComponents($currDepth, $targetDepth, &$itemComponentStack) {
        for ($i=$currDepth; $i>$targetDepth; $i--) {
//            $itemComponents = $this->createItemComponents($itemViewModelStack[$i]);
            $levelComponent = $this->createLevelComponent($targetDepth, $itemComponentStack[$i]);
            unset($itemComponentStack[$i]);
            if (isset($itemComponentStack[$i-1])) {
                $comp = end($itemComponentStack[$i-1]);
                $comp->appendComponentView($levelComponent, ItemComponentInterface::LEVEL);
            }
        }
    }

    /**
     * Vytvoří poslední level component a vloží jej do MenuComponentu
     * 
     * @param type $itemComponentStack
     */
    private function createLastLevelChildrenComponents(&$itemComponentStack) {
//            $itemComponents = $this->createItemComponents($itemViewModelStack[$this->rootRealDepth]);
            $levelComponent = $this->createLevelComponent($this->rootRealDepth, $itemComponentStack[$this->rootRealDepth]);
            $this->appendComponentView($levelComponent, MenuComponentInterface::MENU);
    }

    /**
     * Vytvoří LevelComponent a připojí mu kolekci vnořených ItemComponent
     * 
     * @param type $targetDepth
     * @param type $itemComponents
     * @return LevelComponentInterface
     */
    private function createLevelComponent($targetDepth, $itemComponents) {
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
    
    /**
     * 
     * @param type $itemComponentStackLevel
     * @return array of ItemComponentInterface
     */
//    private function createItemComponents($targetDepth, $itemComponentStackLevel): array {
//        $items = [];
//        foreach ($itemViewModelStackLevel as $itemViewModel) {
//            /** @var ItemViewModelInterface $itemViewModel */
//            $item = new ItemComponent($this->configuration);
//            /** @var DriverComponent $driver */
//            $driver = new DriverComponent($this->configuration);
//            $item->setData($itemViewModel)->setRendererContainer($this->rendererContainer);
//            $driver->setData($itemViewModel->g!!!)->setRendererContainer($this->rendererContainer);
//            if($this->editableMode) {
//                $item->setRendererName($this->itemEditableRendererName);
//                // u kořenového item menu nejsou buttony
//                if ($itemViewModel->isPresented() AND !$itemViewModel->isRoot()) {
//                    $itemButtons = $this->createItemButtonsComponent();
//                    $item->appendComponentView($itemButtons, ItemComponentInterface::ITEM_BUTTONS);
//                }
//            } else {
//                $item->setRendererName($this->itemRendererName);
//            }
//            $nextLevel = $itemViewModel->getLevel();
//            if (isset($nextLevel)) {
//                $item->appendComponentView($nextLevel, ItemComponentInterface::LEVEL);
//            }
//            $items[] = $item;
//        }
//        return $items;
//    }

    private function createItemButtonsComponent(): ItemButtonsComponent {
        $itemButtons = new ItemButtonsComponent($this->configuration);  // typu InheritData - dědí ItemViewModel
        $cut = $this->contextData->getPostCommand('cut') ? true : false;
        $copy = $this->contextData->getPostCommand('copy') ? true :false;
        $pasteMode = ($cut OR $copy);

        #### buttons ####
        $buttonComponents = [];
        switch ((new ItemRenderTypeEnum())($this->contextData->getItemType())) {
            // ButtonsXXX komponenty jsou typu InheritData - dědí ItemViewModel
            case ItemRenderTypeEnum::MULTILEVEL:
                $buttonComponents[] = $this->createItemManipulationButtons();
                $buttonComponents[] = $this->createAddOrPasteMultilevelButtons($pasteMode);
                $buttonComponents[] = $this->createCutCopyButtons($pasteMode);
                break;
            case ItemRenderTypeEnum::ONELEVEL:
                $buttonComponents[] = $this->createItemManipulationButtons();
                $buttonComponents[] = $this->createAddOrPasteOnelevelButtons($pasteMode);
                $buttonComponents[] = $this->createCutCopyButtons($pasteMode);
                break;
            case ItemRenderTypeEnum::TRASH:
                $buttonComponents[] = $this->createCutCopyButtons($pasteMode);
                $buttonComponents[] = $this->createDeleteButtons();
                break;
            default:
                throw new LogicException("Nerozpoznán typ položek menu (hodnota vrácená metodou viewModel->getItemType())");
                break;
        }
        $itemButtons->appendComponentViewCollection($buttonComponents);
        return $itemButtons;
    }

    private function createItemManipulationButtons() {
        return $this->setRenderingByAccess(new ButtonsMenuItemManipulationComponent($this->configuration), ButtonsItemManipulationRenderer::class);
    }

    private function createDeleteButtons() {
        return $this->setRenderingByAccess(new ButtonsMenuDeleteComponent($this->configuration), ButtonsMenuDeleteRenderer::class);
    }

    private function createCutCopyButtons($pasteMode) {
        if ($pasteMode) {
            return $this->setRenderingByAccess(new ButtonsMenuCutCopyComponent($this->configuration), ButtonsMenuCutCopyEscapeRenderer::class);
        } else {
            return $this->setRenderingByAccess(new ButtonsMenuCutCopyComponent($this->configuration), ButtonsMenuCutCopyRenderer::class);
        }
    }

    private function createAddOrPasteOnelevelButtons($pasteMode) {
        if ($pasteMode) {
            return $this->setRenderingByAccess(new ButtonsMenuAddComponent($this->configuration), ButtonsMenuPasteOnelevelRenderer::class);
        } else {
            return $this->setRenderingByAccess(new ButtonsMenuAddComponent($this->configuration), ButtonsMenuAddOnelevelRenderer::class);
        }
    }

    private function createAddOrPasteMultilevelButtons($pasteMode) {
        if ($pasteMode) {
            return $this->setRenderingByAccess(new ButtonsMenuAddComponent($this->configuration), ButtonsMenuPasteMultilevelRenderer::class);
        } else {
            return $this->setRenderingByAccess(new ButtonsMenuAddComponent($this->configuration), ButtonsMenuAddMultilevelRenderer::class);
        }
    }

    private function setRenderingByAccess(ViewInterface $component, $rendererName) {
        /** @var AccessPresentationInterface $accessPresentation */
        $accessPresentation = $this->container->get(AccessPresentation::class);
        if($accessPresentation->isAllowed(get_class($component), AccessPresentationEnum::EDIT)) {
            $component->setRendererName($rendererName);
        } else {
            $component->setRendererName(NoPermittedContentRenderer::class);
        }
        $component->setRendererContainer($this->rendererContainer);
        return $component;
    }
}
