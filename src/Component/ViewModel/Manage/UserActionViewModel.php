<?php
namespace Component\ViewModel\Manage;

use Component\ViewModel\StatusViewModel;

/**
 * Description of UserActionViewModel
 *
 * @author pes2704
 */
class UserActionViewModel extends StatusViewModel {
    public function getIterator() {
        $this->appendData([
                        'editContent' => $this->presentEditableContent(),
                        'editMenu' => $this->presentEditableMenu(),
                        'userName' => $this->getUserLoginName()
        ]);
        return parent::getIterator();
    }

}
