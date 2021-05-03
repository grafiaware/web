<?php

namespace Component\ViewModel;

use Module\Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};

/**
 * Description of StatusViewModelAbstract
 *
 * @author pes2704
 */
class StatusViewModelAbstract {

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
}
