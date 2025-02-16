<?php

namespace Component\ViewModel;

use Red\Model\Entity\EditorActionsInterface;
use Events\Model\Entity\RepresentationActionsInterface;
use Red\Model\Entity\ItemActionInterface;
use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\LanguageInterface;
use Red\Model\Repository\ItemActionRepo;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;

use Status\Model\Entity\StatusSecurityInterface;
use Status\Model\Entity\StatusPresentationInterface;
use Status\Model\Entity\StatusFlashInterface;


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
    
    private $statusSecurity;
    
    private $statusPresentation;
    
    private $statusFlash;

    /**
     * @var ItemActionRepo
     */
//    protected $itemActionRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo
//            ,
//            ItemActionRepo $itemActionRepo
            ) {
        parent::__construct();
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;
        $this->statusFlashRepo = $statusFlashRepo;
//        $this->itemActionRepo = $itemActionRepo;
        $this->statusSecurity = $this->statusSecurityRepo->get();
        $this->statusPresentation = $this->statusPresentationRepo->get();
        $this->statusFlash = $this->statusFlashRepo->get();
        }

    public function getFlashCommand($key) {
        $flashCommand = $this->statusFlashRepo->get()->getCommand();
        return $flashCommand[$key] ?? '';
    }

    public function getFlashPostCommand($key) {
        $flashCommand = $this->statusFlashRepo->get()->readPostCommand();
        return $flashCommand[$key] ?? '';
    }

    public function getFlashMessages() {
        return $this->statusFlashRepo->get()->getMessages();
    }

    public function getUserRole(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return isset($loginAggregate) ? $loginAggregate->getCredentials()->getRoleFk() : null;
    }

    public function getUserLoginName(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return isset($loginAggregate) ? $loginAggregate->getLoginName() : null;
    }
    public function getUserLoginHash(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return isset($loginAggregate) ? $loginAggregate->getLoginNameHash() : null;
    }
    public function getUserEmail(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        $registration = isset($loginAggregate) ? $loginAggregate->getRegistration() : null;  
        return isset($registration) ? $registration->getEmail() : null;
    }
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
    public function presentEditableContent(): bool {
        $editorActions = $this->statusSecurityRepo->get()->getEditorActions();
        return $editorActions ? $editorActions->presentEditableContent() : false;
    }

    public function getPresentedLanguage(): ?LanguageInterface {
        return $this->statusPresentationRepo->get()->getLanguage();
    }

    public function getEditorActions(): ?EditorActionsInterface {
        return $this->statusSecurityRepo->get()->getEditorActions();
    }
    
    public function getRepresentativeActions(): ?RepresentationActionsInterface {
        return $this->statusSecurityRepo->get()->getRepresentativeActions();   
    }

    public function getPresentedMenuItem(): ?MenuItemInterface {
        return $this->statusPresentationRepo->get()->getMenuItem();
    }
    
    public function getSecurityInfos(): array {
        return $this->statusSecurityRepo->get()->getInfos();
    }
    
    public function getPresentationInfos(): array {
        return $this->statusPresentationRepo->get()->getInfos();
    }
    
}
