<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\MenuItem;
use Model\Entity\MenuItemInterface;
use Model\Entity\MenuNode;
use Model\Entity\MenuNodeInterface;
use Database\Hierarchy\ReadHierarchyInterface;
use Database\Hierarchy\EditHierarchyInterface;
use Model\Hydrator\HydratorInterface;
use Model\Repository\MenuItemRepo;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class MenuRepo implements RepoReadonlyInterface {

    const SIEBLING = 'SIEBLING';
    const CHILD = 'CHILD';
    const LASTCHILD = 'LASTCHILD';

    /**
     * @var MenuNodeInterface array of
     */
    private $menuNodeCollection = [];

    /**
     * @var ReadHierarchyInterface
     */
    private $readHierarchy;

    /**
     * @var EditHierarchyInterface
     */
    private $editHierarchy;

    /**
     * @var HydratorInterface
     */
    private $menuItemHydrator;

    /**
     * @var HydratorInterface
     */
    private $menuNodeHydrator;

    /**
     * @var MenuItemRepo
     */
    private $menuItemRepo;

    private $onlyPublished = false;

    public function __construct(ReadHierarchyInterface $readHirerarchy, EditHierarchyInterface $editHierarchy,
            HydratorInterface $menuNodeHydrator, HydratorInterface $menuItemHydrator,
            MenuItemRepo $menuItemRepo
            ) {
        $this->readHierarchy = $readHirerarchy;
        $this->editHierarchy = $editHierarchy;
        $this->menuNodeHydrator = $menuNodeHydrator;
        $this->menuItemHydrator = $menuItemHydrator;
        $this->menuItemRepo = $menuItemRepo;

        // menu repo je vždy readonly - pokud je menuItemRepo pouřito v menuRepo je zde nastaveno také na readonly!!
        // při získávání menuItemRepo ze služby kontejneru předpokládám, že nebude současně použito ukládání menuItem
        $this->menuItemRepo->setReadOnly(true);
    }


//         * @param bool $active Podud je TRUE, vybírá jen položky s vlastností active=TRUE, jinak vrací aktivní i neaktivní.
//     * @param bool $actual Pokud je TRUE, vybírá jen položky kde dnešní datum je mezi show_time a hide_time včetně.
    public function setOnlyPublishedMode($onlyPublished = true) {
        $this->onlyPublished = $onlyPublished;
    }

    /**
     * Vrací entitu vyhledanou podle primárního (kompozitního) klíče: (langCode, uid) s podmínkou active a actual.
     * Podud je parametr active TRUE, vybírá jen publikované položky s vlastností active=TRUE, jinak vrací všechny - aktivní i neaktivní.
     * Pokud je parametr actual=TRUE, vybírá jen položky kde dnešní datum je mezi datumy show_time a hide_time včetně, jinak vrací všechny.
     *
     * @param string $langCode Identifikátor language
     * @param string $uid Identifikátor menu_nested_set
     * @return MenuNodeInterface|null
     */
    public function get($langCode, $uid): ?MenuNodeInterface {
        $index = $langCode.$uid;
        if (!isset($this->menuNodeCollection[$index])) {
            $row = $this->readHierarchy->getNode($langCode, $uid, $this->onlyPublished, $this->onlyPublished);
            $this->recreateEntity($index, $row);
        }
        return $this->menuNodeCollection[$index] ?? null;
    }

    /**
     * JEN POMOCNÁ METODA PRO LADĚNÍ
     * Vrací item podle hodnoty lang_code a title v tabulce menu_item.
     * Vybírá case insensitive. Pokud je více položek se stejným title, vrací jen první.
     *
     * @param string $langCode Identifikátor language
     * @param string $title
     * @return MenuNodeInterface
     */
    public function getNodeByTitle($langCode, $title) {
        $row = $this->readHierarchy->getNodeByTitle($langCode, $title, $this->onlyPublished, $this->onlyPublished);
        $index = $langCode.$row['uid_fk']; // index z parametru a row
        if (!isset($this->menuNodeCollection[$index])) {
            $this->recreateEntity($index, $row);
        }
        return $this->menuNodeCollection[$index] ?? null;

    }

    /**
     * Vrací pole položek "dětí", tj. přímých potomků vyhledanou podle rodiče - primárního (kompozitního) klíče: (langCode, uid) s podmínkou active a actual.
     * Podud je parametr active TRUE, vybírá jen položky s vlastností active=TRUE, jinak vrací aktivní i neaktivní.
     * Pokud je parametr actual=TRUE, vybírá jen položky kde dnešní datum je mezi show_time a hide_time včetně.
     *
     * @param string $langCode Identifikátor language
     * @param string $parentUid Identifikátor rodiče z menu_nested_set
     * @return MenuNodeInterface array of
     */
    public function findChildren($langCode, $parentUid) {
        $children = [];
        foreach($this->readHierarchy->getImmediateSubNodes($langCode, $parentUid, $this->onlyPublished, $this->onlyPublished) as $row) {
            $index = $this->indexFromRow($row);
            $this->recreateEntity($index, $row);
            $children[] = $this->menuNodeCollection[$index];
        }
        return $children;
    }

    /**
     *
     * @param string $langCode Identifikátor language
     * @return MenuNodeInterface array of
     */
    public function getFullTree($langCode) {
        $tree = [];
        foreach($this->readHierarchy->getFullTree($langCode, $this->onlyPublished, $this->onlyPublished) as $row) {
            $index = $this->indexFromRow($row);
            $this->recreateEntity($index, $row);
            $tree[] = $this->menuNodeCollection[$index];
        }
        return $tree;
    }

    /**
     *
     * @param string $langCode Identifikátor language
     * @param string $rootUid
     * @param int $maxDepth int or NULL
     * @return MenuNodeInterface array of
     */
    public function getSubTree($langCode, $rootUid, $maxDepth=NULL) {
        $subTree = [];
        foreach($this->readHierarchy->getSubTree($langCode, $rootUid, $this->onlyPublished, $this->onlyPublished, $maxDepth) as $row) {
            $index = $this->indexFromRow($row);
            $this->recreateEntity($index, $row);
            $subTree[] = $this->menuNodeCollection[$index];
        }
        return $subTree;
    }

    /**
     *
     * @param string $langCode Identifikátor language
     * @param string $parentUid
     * @param int $maxDepth int or NULL
     * @return MenuNodeInterface array of
     */
    public function getSubNodes($langCode, $parentUid, $maxDepth=NULL) {
        $subTree = [];
        foreach($this->readHierarchy->getSubNodes($langCode, $parentUid, $this->onlyPublished, $this->onlyPublished, $maxDepth) as $row) {
            $index = $this->indexFromRow($row);
            $this->recreateEntity($index, $row);
            $subTree[] = $this->menuNodeCollection[$index];
        }
        return $subTree;
    }

    /**
     * K MenuNode objektu zadanému jako predecessor přidá sourozence nebo potomka jako prvního zleva resp. shora nebo nebo potomka jako posledního zleva resp. shora.
     * Všechny vlastnosti MenuNode objektu jsou generované, tato metoda všechny vlastnosti přidávaného MenuNode přepíše. Očekává nový prázdný objekt.
     * @param MenuNodeInterface $menuNode Přidávaný MenuNode objekt
     * @param MenuNodeInterface $predecessorMenuNode Předchůdce - k němu bude přidávaný MenuNode objekt přidán jako jeho sourozenec nebo potomek.
     * @param string $position Hodnota konstanty:
     *  - MenuRepo::SIEBLING
     *  - MenuRepo::CHILD
     *  - MenuRepo::LASTCHILD
     *
     * @throws UnexpectedValueException Pro neznámou hodnotu parametru position
     */
    public function add(MenuNodeInterface $menuNode, MenuNodeInterface $predecessorMenuNode, $position = self::SIEBLING) {
        // metody editHierarchy addXXX pomocí hooked actoru přidají i položku do menu_item (ne do paper)
        switch ($position) {
            case self::SIEBLING:
                $uid = $this->editHierarchy->addNode($predecessorMenuNode->getLeftNode());
                break;
            case self::CHILD:
                $uid = $this->editHierarchy->addChildNode($predecessorMenuNode->getLeftNode());
                break;
            case self::LASTCHILD:
                $uid = $this->editHierarchy->addChildNodeAsLast($predecessorMenuNode->getLeftNode());
                break;
            default:
                throw \UnexpectedValueException("Neznámá pozice přidávané položky.");
        }
        $this->menuNodeHydrator->hydrate($menuNode, $this->readHierarchy->getNode($langCode, $uid, true, true));  // hydratuje menuNode daty zpětně načtenými z databáze
    }

    public function remove(MenuNodeInterface $menuNode) {
        assert(false, "Neimplementováno!");
//        kaskádně - paper nebo block -> menu_item -> nested_set ?
//        nebo odmítnout smazat pokud je paper nebo block - message, pokud není smazat menu_item -> nested_set
        $this->editHierarchy->deleteLeafNode($menuNode->getUid());
    }

    /**
     *
     * @param array $row
     * @return string index
     */
    private function recreateEntity($index, $row) {
        if ($row) {
            if (!isset($this->menuNodeCollection[$index])) {
                $menuItem = new MenuItem();
                $this->menuItemHydrator->hydrate($menuItem, $row);
                $menuItem->setPersisted();
                $this->menuItemRepo->add($menuItem);

                $menuNode = new MenuNode();
                $this->menuNodeHydrator->hydrate($menuNode, $row);
                $menuNode->setMenuItem($menuItem);
                $menuNode->setPersisted();
                $this->menuNodeCollection[$index] = $menuNode;
            }
        }
    }

    private function indexFromEntity(MenuNodeInterface $menuNode) {
        return $menuNode->getMenuItem()->getLangCodeFk().$menuNode->getUid();
    }

    private function indexFromRow($row) {
        return $row['lang_code_fk'].$row['uid'];
    }

}
