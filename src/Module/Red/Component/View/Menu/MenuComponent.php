<?php

namespace Red\Component\View\Menu;

use Psr\Container\ContainerInterface;

use Component\View\ComponentCompositeAbstract;

use Configuration\ComponentConfigurationInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Component\ViewModel\Menu\MenuViewModelInterface;
use Red\Component\ViewModel\Menu\LevelViewModelInterface;

use Red\Component\View\Menu\LevelComponent;
use Red\Component\View\Menu\LevelComponentInterface;
use Red\Component\View\Menu\ItemComponent;
use Red\Component\View\Menu\ItemComponentInterface;
use Red\Component\View\Menu\DriverComponent;
use Red\Component\View\Menu\DriverComponentInterface;


use Red\Service\Menu\DriverService;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

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
    private $levelRendererEditableName;
    
    private $rootRealDepth;
    
    private $presentedMenuItemId;

    public function __construct(ComponentConfigurationInterface $configuration, ContainerInterface $container) {
        parent::__construct($configuration);
        $this->container = $container;
    }
    
    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => static::class]
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
    public function setRenderersNames($levelRendererName, $levelRendererEditableName): MenuComponentInterface {
        $this->levelRendererName = $levelRendererName;
        $this->levelRendererEditableName = $levelRendererEditableName;
        return $this;
    }

    /**
     * Připraví MenuComponent a strom všech jejích potomků k renderování
     * 
     * @return void
     * @throws \LogicException
     */
    public function beforeRenderingHook(): void {
        $this->setPresentedMenuItemId();
        $subtreeNodeModels = $this->contextData->getNodeModels();
        $topLevelComponent = $this->buildMenuComponentsTree($subtreeNodeModels);
        $this->appendComponentView($topLevelComponent, MenuComponentInterface::MENU);        
    }

    private function setPresentedMenuItemId(): void {
        /** @var MenuItemInterface $presentedMenuItem */
        $presentedMenuItem = $this->contextData->getPresentedMenuItem();
        $this->presentedMenuItemId = isset($presentedMenuItem) ? $presentedMenuItem->getId() : null;
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
        
        $itemComponentStack = [];
        $first = true;
        foreach ($subtreeNodeModels as $treeNodeModel) {
            $itemDepth = $treeNodeModel['realDepth'];
            if ($first) {
                $this->rootRealDepth = $itemDepth;
                $currDepth = $itemDepth;
                $first = false;
            }
            if ($itemDepth>$currDepth) {
                $currDepth = $itemDepth;
            } elseif ($itemDepth<$currDepth) {
                $this->createChildrenComponents($currDepth, $itemDepth, $itemComponentStack);
                $currDepth = $itemDepth;
            }
            $itemComponentStack[$currDepth][] = $this->createItemComponent($treeNodeModel);
        }
        if ($itemComponentStack) {
            $levelComponent = $this->createChildrenComponents($currDepth, $this->rootRealDepth-1, $itemComponentStack);
        } else {
            $levelComponent = $this->createLevelComponent([]);
        }
        return $levelComponent;
    }
    
    /**
     * Nový ItemComponent
     * 
     * @param array $treeNodeModel
     * @return ItemComponent
     */
    private function createItemComponent($treeNodeModel) {
        /** @var ItemComponent $item */
        $item = $this->container->get(ItemComponent::class);
        $itemViewModel = $item->getData();
        $itemViewModel->setUid($treeNodeModel['menuItem']->getUidFk());
        $itemViewModel->setRealDepth($treeNodeModel['realDepth']);
        $itemViewModel->setOnPath($treeNodeModel['isOnPath']);
        $itemViewModel->setLeaf($treeNodeModel['isLeaf']);
        $driver = $this->createDriverComponent($treeNodeModel['menuItem']);
        $item->appendComponentView($driver, ItemComponentInterface::DRIVER);        
        return $item;
    }
    
    /**
     * Nový DriverComponent
     * 
     * @param MenuItemInterface $menuItem
     * @return DriverComponentInterface
     */
    public function createDriverComponent(MenuItemInterface $menuItem): DriverComponentInterface {   // PUBLIC pro volání z ComponentControler
        /** @var DriverComponent $driver */
        $driver = $this->container->get(DriverComponent::class);
        /** @var DriverServiceInterface $driverService */
        $driverService = $this->container->get(DriverService::class);
        $isPresented = $this->presentedMenuItemId == $menuItem->getId();        
        $driverService->completeDriverComponent($driver, $menuItem->getUidFk(), $isPresented);
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
            $levelComponent = $this->createLevelComponent($itemComponentStack[$i]);
            unset($itemComponentStack[$i]);
            if (isset($itemComponentStack[$i-1])) {
                $parentItemComponent = end($itemComponentStack[$i-1]);
                $parentItemComponent->appendComponentView($levelComponent, ItemComponentInterface::LEVEL);
            } else {
                $levelViewModel = $levelComponent->getData();
                /** @var LevelViewModelInterface $levelViewModel */
                $levelViewModel->setLastLevel(true);                
                return $levelComponent;
            }
        }
    }

    /**
     * Vytvoří LevelComponent a připojí mu kolekci vnořených ItemComponent
     * 
     * @param type $itemComponents
     * @return LevelComponentInterface
     */
    private function createLevelComponent(iterable $itemComponents): LevelComponentInterface {
        /** @var LevelComponentInterface $levelComponent */
        $levelComponent = $this->container->get(LevelComponent::class);
        $levelComponent->setRendererName($this->contextData->presentEditableContent() ? $this->levelRendererEditableName : $this->levelRendererName);  
//        tady přepínat!  level renderer musí být jiný v editable režimu - chci mír rozbalovací menu v needit režimu a rozbalené menu v edit režimu - to je class na level
//        setRenderersNames() musí dostávat z kontejneru 2 jména: LevelRenderer a LevelRendererEditable
//        část #menu renderer ze configurationStyles -> do RendererContainerConfigurator - přidej definici LevelRendererEditable s classmap:'menuVertical.classmap.editable'
        $levelComponent->appendComponentViewCollection($itemComponents);
        return $levelComponent;
    }


}
