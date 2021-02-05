<?php
namespace Component\View\Generated;

use Component\View\ComponentAbstract;

use Component\ViewModel\Generated\LanguageSelectViewModel;

/**
 * Description of LanguageSelect
 *
 * @author pes2704
 */
class LanguageSelectComponent extends ComponentAbstract {

    /**
     * @var LanguageSelectViewModel
     */
    protected $viewModel;

    public function __construct(LanguageSelectViewModel $viewModel) {
        $this->viewModel = $viewModel;
    }
}
