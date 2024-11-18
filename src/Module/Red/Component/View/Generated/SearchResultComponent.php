<?php
namespace Red\Component\View\Generated;
use Component\View\ComponentCompositeAbstract;
use Red\Component\ViewModel\Generated\SearchResultViewModel;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

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
class SearchResultComponent extends ComponentCompositeAbstract {

    /**
     * @var SearchResultViewModel
     */
    protected $contextData;
    
    public static function getComponentPermissions(): array {
        return [
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
        ];
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
