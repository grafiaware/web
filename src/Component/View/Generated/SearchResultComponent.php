<?php
namespace Component\View\Generated;
use Component\View\ComponentAbstract;
use Component\ViewModel\Generated\SearchResultViewModel;

use Pes\View\Renderer\RendererInterface;

use Pes\Text\Message;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SearchResultComponent
 *
 * @author pes2704
 */
class SearchResultComponent extends ComponentAbstract {

    /**
     * @var SearchResultViewModel
     */
    protected $contextData;

    public function __construct(SearchResultViewModel $viewModel) {
        $this->contextData = $viewModel;
    }

    /**
     * Hledaný text. Hledají se jednotlivá slova IN NATURAL LANGUAGE MODE.
     * @param string $key
     * @return $this
     */
    public function setSearch($key) {
        $this->contextData->setQuery($key);
        return $this;
    }
}
