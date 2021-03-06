<?php
namespace Component\ViewModel\Authored\Paper;

use Model\Entity\MenuItemInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;
use Model\Repository\PaperAggregateRepo;
use Model\Repository\MenuItemRepo;

/**
 * Description of PresentedPaperViewModel
 *
 * @author pes2704
 */
class ItemPaperViewModel extends PaperViewModel implements ItemPaperViewModelInterface {

    private $menuItemId;
    protected $menuItemRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            PaperAggregateRepo $paperAggregateRepo,
            MenuItemRepo $menuItemRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo, $paperAggregateRepo);
        $this->menuItemRepo = $menuItemRepo;
    }

    public function setItemId($menuItemId) {
        $this->menuItemId = $menuItemId;
    }
    public function getPaper(): ?PaperAggregatePaperContentInterface {
            $paperAggregate = $this->paperAggregateRepo->getByReference($this->menuItemId);
            if (!isset($paperAggregate) AND $this->isArticleEditable()) {
                $paperAggregate = new PaperAggregate();
                $paperAggregate->setEditor($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());
            }
        return $paperAggregate ?? null;
    }
    public function getMenuItem(): ?MenuItemInterface {
        return $this->menuItemRepo->getByReference($this->menuItemId);
    }

    public function getInfo(): string {
        return "item";
    }

}
