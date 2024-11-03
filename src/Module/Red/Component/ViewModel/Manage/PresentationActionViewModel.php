<?php
namespace Red\Component\ViewModel\Manage;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;
use Red\Component\ViewModel\Manage\PresentationActionViewModelInterface;

use ArrayIterator;

/**
 * Description of UserActionViewModel
 *
 * @author pes2704
 */
class PresentationActionViewModel extends ViewModelAbstract implements PresentationActionViewModelInterface {

    private $statusViewModel;

    public function __construct(
            StatusViewModelInterface $status) {
        $this->statusViewModel = $status;
    }

    public function getIterator() {
        return new ArrayIterator([
                        'editContent' => $this->statusViewModel->presentEditableContent(),
                        'loginName' => $this->statusViewModel->getUserLoginName()
        ]);
    }

}
