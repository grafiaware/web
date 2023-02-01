<?php
namespace Red\Component\ViewModel\Manage;

use Red\Component\ViewModel\ViewModelAbstract;
use Red\Component\ViewModel\StatusViewModelInterface;

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
                        'editMenu' => $this->status->presentEditableMenu(),
                        'userName' => $this->status->getUserLoginName()
        ]);
    }

}
