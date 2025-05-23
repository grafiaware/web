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

use Pes\Debug\Timer;


/**
 * Description of MenuComponent
 *
 * @author pes2704
 */
abstract class MenuComponentAbstract extends ComponentCompositeAbstract implements MenuComponentInterface {

    private $container;
    private $itemType;

    /**
     * @var MenuViewModelInterface
     */
    protected $contextData;

    private $levelRendererName;
    private $levelRendererEditableName;
        
    private $presentedMenuItemId;

    public function __construct(ComponentConfigurationInterface $configuration, ContainerInterface $container) {
        parent::__construct($configuration);
        $this->container = $container;
    }
    
    public function setItemType($itemType) {
        $this->itemType = $itemType;        
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
        if (isset($this->contextData)) {
            $this->setPresentedMenuItemId();
                $timer = new Timer();
                $timer->start();
            $subtreeNodeModels =  $this->contextData->getNodeModels();
                $modelsTime = $timer->interval();  // 150 milisec
            $topLevelComponent = $this->buildMenuComponentsTree($subtreeNodeModels);
                $runtime = $timer->runtime();  // 525 milisec
            $this->appendComponentView($topLevelComponent, MenuComponentInterface::MENU);    
        }
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
            $timer = new Timer();
            $timer->start();   
            $ii=0;
        foreach ($subtreeNodeModels as $treeNodeModel) {
            $itemDepth = $treeNodeModel['realDepth'];
            if ($first) {
                $rootRealDepth = $itemDepth;
                $currDepth = $itemDepth;
                $first = false;
            }
            if ($itemDepth>$currDepth) {
                $currDepth = $itemDepth;
            } elseif ($itemDepth<$currDepth) {
                $this->createChildrenComponents($currDepth, $itemDepth, $itemComponentStack);
                $forea[$ii++.'children'] = $timer->interval();            // 13 milisec
                $currDepth = $itemDepth;
            }
            $itemComponentStack[$currDepth][] = $this->createItemComponent($treeNodeModel);
                $forea[$ii++.'item'] = $timer->interval();            // 13 milisec
        }
        if ($itemComponentStack) {
            $levelComponent = $this->createChildrenComponents($currDepth, $rootRealDepth-1, $itemComponentStack);
        } else {
            $levelComponent = $this->createLevelComponent([]);
        }
            $runtime = $timer->runtime();        // 520 milisec
        return $levelComponent;
    }
    
    /**
     * Nový ItemComponent
     * 
     * @param array $treeNodeModel
     * @return ItemComponent
     */
    private function createItemComponent($treeNodeModel) {
            $timer = new Timer();
            $timer->start();        
            $it = 0;
        /** @var ItemComponent $item */
        $item = $this->container->get(ItemComponent::class);
            $containerTime = $timer->interval();
        $itemViewModel = $item->getData();
        $itemViewModel->setUid($treeNodeModel['menuItem']->getUidFk());
        $itemViewModel->setRealDepth($treeNodeModel['realDepth']);
        $itemViewModel->setOnPath($treeNodeModel['isOnPath']);
        $itemViewModel->setLeaf($treeNodeModel['isLeaf']);
            $dataTime = $timer->interval();
        $driver = $this->createDriverComponent($treeNodeModel['menuItem']);
            $driveTime = $timer->interval();
        $item->appendComponentView($driver, ItemComponentInterface::DRIVER);        
                $itemTime = $timer->runtime();         
        return $item;
    }
    
    /**
     * Nový DriverComponent
     * 
     * @param MenuItemInterface $menuItem
     * @return DriverComponentInterface
     */
    private function createDriverComponent(MenuItemInterface $menuItem): DriverComponentInterface {   // ?? PUBLIC pro volání z ComponentControler
        /** @var DriverComponent $driver */
            $timer = new Timer();
            $timer->start();          
        $driver = $this->container->get(DriverComponent::class);
            $containerCompTime = $timer->interval();  // poprvé 1ms, pak 80 mikrosec
        /** @var DriverServiceInterface $driverService */
        $driverService = $this->container->get(DriverService::class);
        $isPresented = $this->presentedMenuItemId == $menuItem->getId();
        $driverService->completeDriverComponent($driver, $menuItem, $isPresented, $this->itemType); //  $itemType!
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
     * Nastaví level renderer podle podle prezentačního režimu editovatelný/needitovatelný
     * 
     * @param type $itemComponents
     * @return LevelComponentInterface
     */
    private function createLevelComponent(iterable $itemComponents): LevelComponentInterface {
        /** @var LevelComponentInterface $levelComponent */
        $levelComponent = $this->container->get(LevelComponent::class);
        $levelComponent->setRendererName($this->contextData->presentEditableContent() ? $this->levelRendererEditableName : $this->levelRendererName);  
        $levelComponent->appendComponentViewCollection($itemComponents);
        return $levelComponent;
    }


}
