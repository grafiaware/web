<?php
namespace Web\Component\ViewModel\Manage;

use Web\Component\ViewModel\StatusViewModel;

/**
 * Description of ToggleEditMenuViewModel
 *
 * @author pes2704
 */
class EditMenuSwitchViewModel extends StatusViewModel implements ToggleEditMenuViewModelInterface {
    public function getIterator() {
        $this->appendData([
            'editMenu' => $this->presentEditableMenu(),
        ]);
        return parent::getIterator();
    }

}
