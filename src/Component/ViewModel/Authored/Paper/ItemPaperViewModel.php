<?php
namespace Component\ViewModel\Authored\Paper;

use Model\Entity\MenuItemInterface;

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
class ItemPaperViewModel extends PaperViewModelAbstract implements ItemPaperViewModelInterface {

    private $uidFk;
    private $langCodeFk;
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

    public function setItemParams($langCodeFk, $uidFk) {
        $this->langCodeFk = $langCodeFk;
        $this->uidFk = $uidFk;
    }

    public function getMenuItem(): ?MenuItemInterface {
        return $this->menuItemRepo->get($this->langCodeFk, $this->uidFk);
    }

    public function getInfo(): string {
        return "presented";
    }

}
