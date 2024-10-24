<?php
namespace Events\Component\ViewModel\Manage;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;

use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class RepresentativeActionViewModel extends ViewModelAbstract {

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
