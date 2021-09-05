<?php

namespace Component\ViewModel;

use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};

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

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo
            ) {
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;
        $this->statusFlashRepo = $statusFlashRepo;
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

    public function presentEditableArticle(): bool {
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        return $userActions ? $userActions->presentEditableArticle() : false;
    }

    public function presentEditableLayout(): bool {
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        return $userActions ? $userActions->presentEditableLayout() : false;
    }

    public function presentEditableMenu(): bool {
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        return $userActions ? $userActions->presentEditableMenu() : false;
    }

    public function getIterator() {
        $this->appendData([
                        'editArticle' => $this->presentEditableArticle(),
                        'editLayout' => $this->presentEditableLayout(),
                        'editMenu' => $this->presentEditableMenu(),
                        'userName' => $this->getUserLoginName()
        ]);
        return parent::getIterator();
    }
}
