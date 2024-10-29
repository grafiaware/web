<?php
namespace Red\Component\ViewModel\Manage;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;

use ArrayIterator;

/**
 * Description of UserActionViewModel
 *
 * @author pes2704
 */
class UserActionViewModel extends ViewModelAbstract {

    private $status;

    public function __construct(
            StatusViewModelInterface $status) {
        $this->status = $status;
    }

    public function getIterator() {
        return new ArrayIterator([
                        'editContent' => $this->status->presentEditableContent(),
                        'loginName' => $this->status->getUserLoginName()
        ]);
    }

}
