<?php
namespace Component\ViewModel\Statical;

use Component\ViewModel\StatusViewModelAbstract;

use Red\Model\Entity\HierarchyAggregateInterface;
use Red\Model\Entity\MenuRootInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\MenuItemRepo;

/**
 * Description of MenuViewModel
 *
 * @author pes2704
 */
class FolderSelectViewModel extends StatusViewModelAbstract implements FolderSelectViewModelInterface {

    /**
     *
     * @var MenuItemRepo
     */
    private $menuItemRepo;
    private $hierarchyRepo;
    private $presentedMenuNode;
    private $withRoot = false;
    private $menuRootBlockName;
    private $maxDepth;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            MenuItemRepo $hierarchyRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->menuItemRepo = $menuItemRepo;
    }

    /**
     * Nstaví jméni bloku, jehož ko
     * @param string $blockName
     * @return void
     */
    public function setMenuRootName($blockName): void {
        $this->menuRootBlockName = $blockName;
    }

    /**
     *
     * @param bool $withTitle
     * @return void
     */
    public function withTitleItem($withTitle=false): void {
        $this->withRoot = $withTitle;
    }

    /**
     *
     * @param int $maxDepth
     * @return void
     */
    public function setMaxDepth($maxDepth): void {
        $this->maxDepth = $maxDepth;
    }

    /**
     * {@inheritdoc}
     *
     * @return HierarchyAggregateInterface|null
     */
    public function getPresentedMenuNode(HierarchyAggregateInterface $rootNode): ?HierarchyAggregateInterface { // jen mezi left a rifht - uprav
        if(!isset($this->presentedMenuNode)) {
            $presentationStatus = $this->statusPresentationRepo->get();
            $presentedMenuItem = $presentationStatus->getMenuItem();
            if ($presentedMenuItem) {
                $presented = $this->getMenuNode($presentedMenuItem->getUidFk());
                if ($presented->getLeftNode() >= $rootNode->getLeftNode() AND $presented->getLeftNode() < $rootNode->getRightNode()) {
                    $this->presentedMenuNode = $presented;
                }
            }
        }
        return $this->presentedMenuNode;
    }

public function getMenuItemByStaticName($staticName) {
    return $this->menuItemRepo->
}
    public function getIterator(): \Traversable {
        return new \ArrayObject(
                [
                    'subTreeItemModels' => $this->getSubTreeItemModels(),
                ]
                );
    }
}
