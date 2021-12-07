<?php
namespace Component\ViewModel\Authored\Multipage;

use ArrayObject;
use Component\ViewModel\Authored\AuthoredViewModelAbstract;
use Component\ViewModel\Authored\Multipage\MultipageViewModelInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;

use Red\Model\Repository\MenuItemRepoInterface;
use Red\Model\Repository\ItemActionRepo;
use Red\Model\Repository\MultipageRepo;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;

use Red\Model\Entity\MultipageInterface;
use Red\Model\Entity\MenuItemAggregateHierarchyInterface;

use TemplateService\TemplateSeekerInterface;

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
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            MenuItemRepoInterface $menuItemRepo,
            TemplateSeekerInterface $templateSeeker,
            ItemActionRepo $itemActionRepo,
            MultipageRepo $multipageRepo,
            HierarchyJoinMenuItemRepo $hierarchyRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo, $menuItemRepo, $itemActionRepo, $templateSeeker);
        $this->multipageRepo = $multipageRepo;
        $this->hierarchyRepo = $hierarchyRepo;
    }

    /**
     * {@inheritdoc}
     *
     * MenuItem musí být aktivní nebo prezentace musí být v režimu editable content - jinak repository nevrací menuItem a nevznikne Multipage, metoda vrací null.
     *
     * @return MultipageInterface|null
     */
    public function getMultipage(): ?MultipageInterface {
        if (isset($this->menuItemIdCached)) {
            $multipage = $this->multipageRepo->getByReference($this->menuItemIdCached);
        }
        return $multipage ?? null;
    }

    /**
     * Vrací pole uzlů HierarchyAggregateInterface[], obsahuje uzel (node) odpovídající multipage a všechny potomky (neomezená hloubka)
     * 
     * @return MenuItemAggregateHierarchyInterface[]
     */
    public function getSubNodes() {
        $multipage = $this->getMultipage();
        if (isset($multipage)) {
            $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
            $menuItem = $this->menuItemRepo->getById($multipage->getMenuItemIdFk());
            $nodes = $this->hierarchyRepo->getSubNodes($langCode, $menuItem->getUidFk());  // neomezená maxDepth
            return $nodes;
        }
    }

    public function getIterator() {
        $this->appendData(['multipage'=> $this->getMultipage(), 'isEditable'=> $this->presentEditableContent()]);
        return parent::getIterator();
    }


}