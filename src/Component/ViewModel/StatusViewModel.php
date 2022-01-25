<?php

namespace Component\ViewModel;

use Red\Model\Entity\UserActionsInterface;
use Red\Model\Entity\ItemActionInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;

use Red\Model\Repository\ItemActionRepo;
use Red\Model\Repository\MenuItemRepoInterface;

/**
 * Description of StatusViewModelAbstract
 *
 * @author pes2704
 */
class StatusViewModel extends ViewModelAbstract implements StatusViewModelInterface {

    /**
     * @var StatusSecurityRepo
     */
    protected $statusSecurityRepo;

    /**
     * @var StatusPresentationRepo
     */
    protected $statusPresentationRepo;

    /**
     * @var StatusFlashRepo
     */
    protected $statusFlashRepo;

    /**
     *
     * @var ItemActionRepo
     */
    protected $itemActionRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            ItemActionRepo $itemActionRepo
            ) {
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;
        $this->statusFlashRepo = $statusFlashRepo;
        $this->itemActionRepo = $itemActionRepo;
        parent::__construct();
        }

    public function getFlashCommand($key) {
        $flashCommand = $this->statusFlashRepo->get()->getCommand();
        return $flashCommand[$key] ?? '';
    }

    public function getPostFlashCommand($key) {
        $flashCommand = $this->statusFlashRepo->get()->getPostCommand();
        return $flashCommand[$key] ?? '';
    }

    public function isUserLoggedIn(): bool {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return isset($loginAggregate) ? true : false;
    }

    public function getUserRole(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return isset($loginAggregate) ? $loginAggregate->getCredentials()->getRole() : null;
    }

    public function getUserLoginName(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return isset($loginAggregate) ? $loginAggregate->getLoginName() : null;
    }

    public function presentEditableContent(): bool {
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        return $userActions ? $userActions->presentEditableContent() : false;
    }

    public function presentEditableMenu(): bool {
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        return $userActions ? $userActions->presentEditableMenu() : false;
    }

    ### user actions ###

    public function getIterator() {
        $this->appendData([
                        'editArticle' => $this->presentEditableContent(),
                        'editMenu' => $this->presentEditableMenu(),
                        'userName' => $this->getUserLoginName()
        ]);
        return parent::getIterator();
    }
}
