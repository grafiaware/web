<?php
namespace Component\ViewModel\MenuItem\Authored\Multipage;

use ArrayObject;
use Component\ViewModel\MenuItem\Authored\AuthoredViewModelAbstract;
use Component\ViewModel\MenuItem\Authored\Multipage\MultipageViewModelInterface;

use Component\ViewModel\StatusViewModelInterface;

use Red\Model\Repository\MenuItemRepoInterface;

use Red\Model\Repository\MultipageRepo;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;

use Red\Model\Entity\MultipageInterface;
use Red\Model\Entity\MenuItemAggregateHierarchyInterface;

use Red\Model\Enum\AuthoredTypeEnum;
/**
 * Description of PaperViewModelAnstract
 *
 * @author pes2704
 */
class MultipageViewModel extends AuthoredViewModelAbstract implements MultipageViewModelInterface {

    /**
     * @var MultipageRepo
     */
    private $multipageRepo;

    /**
     * @var HierarchyJoinMenuItemRepo
     */
    private $hierarchyRepo;

    public function __construct(
            StatusViewModelInterface $status,
            MenuItemRepoInterface $menuItemRepo,
            MultipageRepo $multipageRepo,
            HierarchyJoinMenuItemRepo $hierarchyRepo
            ) {
        parent::__construct($status, $menuItemRepo);
        $this->multipageRepo = $multipageRepo;
        $this->hierarchyRepo = $hierarchyRepo;
    }

    /**
     * Vrací typ položky. Používá AuthoredEnum.
     * Obvykle je metoda volána z metody Front kontroleru.
     *
     * @param type $menuItemType
     */
    public function getAuthoredContentType(): string {
        return AuthoredTypeEnum::MULTIPAGE;
    }

    public function getAuthoredTemplateName(): ?string {
        $multipage = $this->getMultipage();
        return isset($multipage) ? $multipage->getTemplate() : null;
    }

    public function getAuthoredContentId(): string {
        return $this->getMultipage()->getId();
    }

    /**
     * {@inheritdoc}
     *
     * MenuItem musí být aktivní nebo prezentace musí být v režimu editable content - jinak repository nevrací menuItem a nevznikne Multipage, metoda vrací null.
     *
     * @return MultipageInterface|null
     */
    public function getMultipage(): ?MultipageInterface {
        return $this->multipageRepo->getByReference($this->getMenuItem()->getUidFk());
    }

    /**
     * Vrací pole uzlů HierarchyAggregateInterface[], obsahuje uzel (node) odpovídající multipage a všechny potomky (neomezená hloubka)
     *
     * @return MenuItemAggregateHierarchyInterface[]
     */
    public function getSubNodes() {
        $multipage = $this->getMultipage();
        if (isset($multipage)) {
            $langCode = $this->getStatus()->getPresentedLanguage()->getLangCode();
            $menuItem = $this->menuItemRepo->getById($multipage->getMenuItemIdFk());
            $nodes = $this->hierarchyRepo->getSubNodes($langCode, $menuItem->getUidFk());  // neomezená maxDepth
            return $nodes;
        }
    }

//    public function getIterator() {
//        $this->appendData(['multipage'=> $this->getMultipage()]);
//        return parent::getIterator();
//    }


}