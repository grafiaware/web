<?php
namespace Component\View\Authored\Paper;

use Component\View\Authored\Paper\AuthoredComponentAbstract;

use Component\ViewModel\Authored\Paper\ItemPaperViewModelInterface;

/**
 * Description of NamedItemComponent
 *
 * @author pes2704
 */
class ItemPaperComponent extends AuthoredComponentAbstract implements ItemComponentInterface {

    /**
     *
     * @var ItemPaperViewModelInterface
     */
    protected $viewModel;


    public function __construct(ItemPaperViewModelInterface $viewModel) {
        $this->viewModel = $viewModel;
    }

    public function setItemParams($langCodeFk, $uidFk): ItemComponentInterface {
        $this->viewModel->setItemParams($langCodeFk, $uidFk);
        return $this;
    }
}
