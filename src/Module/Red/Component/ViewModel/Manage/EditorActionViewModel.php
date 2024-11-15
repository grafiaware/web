<?php
namespace Red\Component\ViewModel\Manage;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;
use Red\Component\ViewModel\Manage\EditorActionViewModelInterface;

use ArrayIterator;

/**
 * Description of UserActionViewModel
 *
 * @author pes2704
 */
class EditorActionViewModel extends ViewModelAbstract implements EditorActionViewModelInterface {

    private $statusViewModel;

    public function __construct(
            StatusViewModelInterface $status) {
        $this->statusViewModel = $status;
    }

    public function getIterator() {
        $arr =[
                        'editContent' => $this->statusViewModel->presentEditableContent(),
                        'loginName' => $this->statusViewModel->getUserLoginName()
        ];
        return new ArrayIterator($arr);
    }

}
