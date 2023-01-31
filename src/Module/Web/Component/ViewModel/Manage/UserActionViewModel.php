<?php
namespace Web\Component\ViewModel\Manage;

use Web\Component\ViewModel\ViewModelAbstract;
use Web\Component\ViewModel\StatusViewModelInterface;

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
