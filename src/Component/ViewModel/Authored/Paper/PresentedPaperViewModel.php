<?php
namespace Component\ViewModel\Authored\Paper;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Model\Entity\PaperAggregateInterface;
use Model\Entity\MenuItemInterface;

/**
 * Description of PresentedPaperViewModel
 *
 * @author pes2704
 */
class PresentedPaperViewModel extends PaperViewModelAbstract implements PresentedPaperViewModelInterface {

    public function getMenuItem(): ?MenuItemInterface {
        return $this->statusPresentationRepo->get()->getMenuItem();
    }

}
