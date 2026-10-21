<?php
namespace Web\Component\ViewModel\Flash;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;

use ArrayIterator;

/**
 * Description of FlashVieModel
 *
 * @author pes2704
 */
class FlashViewModel extends ViewModelAbstract implements FlashViewModelInterface {

    private $status;

    public function __construct(StatusViewModelInterface $status) {
        $this->status = $status;
    }

    public function getIterator(): ArrayIterator {
        $this->appendData( [
                    'flashMessages' => $this->status->getFlashMessages(),
                ]
            );
        return parent::getIterator();
    }
}
