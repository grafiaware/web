<?php

namespace Component\ViewModel;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;

use Red\Model\Entity\EditorActionsInterface;
use Events\Model\Entity\RepresentationActionsInterface;
use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\StaticItemInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Entity\SecurityInterface;
use Status\Model\Entity\PresentationInterface;
use Status\Model\Entity\FlashInterface;

/**
 * Description of StatusViewModel
 *
 * @author pes2704
 */
class StatusViewModel extends ViewModelAbstract implements StatusViewModelInterface {
    
    /**
     * 
     * @var ?SecurityInterface
     */
    private $statusSecurity;
    
    /**
     * 
     * @var ?PresentationInterface
     */
    private $statusPresentation;
    
    /**
     * 
     * @var ?FlashInterface
     */
    private $statusFlash;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo
            ) {
        parent::__construct();

        $this->statusSecurity = $statusSecurityRepo->get();
        $this->statusPresentation = $statusPresentationRepo->get();
        $this->statusFlash = $statusFlashRepo->get();
    }

    #[\Override]
    public function getFlashPostCommand($key) {
        $flashCommand = $this->statusFlash->readPostCommand();
        return $flashCommand[$key] ?? '';
    }

    #[\Override]
    public function getFlashMessages() {
        return $this->statusFlashRepo->get()->getMessages();
    }

    #[\Override]
    public function getUserRole(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return isset($loginAggregate) ? $loginAggregate->getCredentials()->getRoleFk() : null;
    }

    #[\Override]
    public function getUserLoginName(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return isset($loginAggregate) ? $loginAggregate->getLoginName() : null;
    }

    #[\Override]
    public function getUserLoginHash(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return isset($loginAggregate) ? $loginAggregate->getLoginNameHash() : null;
    }

    #[\Override]
    public function getUserEmail(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        $registration = isset($loginAggregate) ? $loginAggregate->getRegistration() : null;  
        return isset($registration) ? $registration->getEmail() : null;
    }

    #[\Override]
    public function isUserLoggedIn(): bool {
        $loginAggregate = $this->getUserLoginName();
        return isset($loginAggregate) ? true : false;
    }

    /**
     * {@inheritdoc}
     *
     * Informuje zda prezentace je v editovatelném režimu a současně prezentovaný obsah je editovatelný přihlášeným uživatelem.
     *
     * @return bool
     */
    #[\Override]
    public function presentEditableContent(): bool {
        $editorActions = $this->statusSecurityRepo->get()->getEditorActions();
        return $editorActions ? $editorActions->presentEditableContent() : false;
    }

    #[\Override]
    public function getPresentedLanguageCode(): ?string {
        return $this->statusPresentationRepo->get()->getLanguageCode();
    }

    #[\Override]
    public function getEditorActions(): ?EditorActionsInterface {
        return $this->statusSecurityRepo->get()->getEditorActions();
    }
    
    #[\Override]
    public function getRepresentativeActions(): ?RepresentationActionsInterface {
        return $this->statusSecurityRepo->get()->getRepresentativeActions();   
    }

    #[\Override]
    public function getPresentedMenuItem(): ?MenuItemInterface {
        return $this->statusPresentationRepo->get()->getMenuItem();
    }
    
    #[\Override]
    public function getPresentedStaticItem(): ?StaticItemInterface {
        return $this->statusPresentationRepo->get()->getStaticItem();
    }
    
    #[\Override]
    public function getSecurityInfos(): array {
        return $this->statusSecurityRepo->get()->getInfos();
    }
    
    #[\Override]
    public function getPresentationInfos(): array {
        return $this->statusPresentationRepo->get()->getInfos();
    }
    
}
