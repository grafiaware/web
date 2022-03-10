<?php

namespace Component\ViewModel;

use Red\Model\Entity\UserActionsInterface;
use Red\Model\Entity\ItemActionInterface;
use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\LanguageInterface;
use Red\Model\Repository\ItemActionRepo;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;

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

    public function getFlashPostCommand($key) {
        $flashCommand = $this->statusFlashRepo->get()->getPostCommand();
        return $flashCommand[$key] ?? '';
    }

    public function getFlashMessages() {
        return $this->statusFlashRepo->get()->getMessages();
    }

    public function getUserRole(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return isset($loginAggregate) ? $loginAggregate->getCredentials()->getRole() : null;
    }

    public function getUserLoginName(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return isset($loginAggregate) ? $loginAggregate->getLoginName() : null;
    }

    /**
     * {@inheritdoc}
     *
     * Informuje zda prezentace je v editovatelném režimu a současně prezentovaný obsah je editovatelný přihlášeným uživatelem.
     * Editovat smí uživatel s rolí 'sup'
     *
     * @return bool
     */
    public function presentEditableContent(): bool {
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        return $userActions ? $userActions->presentEditableContent() : false;
    }

    public function presentEditableMenu(): bool {
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        return $userActions ? $userActions->presentEditableMenu() : false;
    }

    public function getPresentedLanguage(): ?LanguageInterface {
        return $this->statusPresentationRepo->get()->getLanguage();
    }

    public function getUserActions(): ?UserActionsInterface {
        return $this->statusPresentationRepo->get()->getUserActions();
    }

    public function getPresentedMenuItem(): ?MenuItemInterface {
        return $this->statusPresentationRepo->get()->getMenuItem();
    }

}
