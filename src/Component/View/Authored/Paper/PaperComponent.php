<?php
namespace Component\View\Authored\Paper;

use Component\View\Authored\Paper\AuthoredComponentAbstract;
use Component\View\Authored\Paper\Article\ArticleComponentInterface;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

/**
 * Description of NamedItemComponent
 *
 * @author pes2704
 */
class PaperComponent extends AuthoredComponentAbstract implements PaperComponentInterface {

    protected $articleComponent;

    public function setItemId($menuItemId): PaperComponentInterface {
        $this->viewModel->setItemId($menuItemId);
        return $this;
    }

}
