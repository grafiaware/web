<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View;

use Pes\View\CompositeView;

use Component\View\ComponentCompositeInterface;

use Configuration\ComponentConfigurationInterface;

//??
use Component\ViewModel\StatusViewModelInterface;

/**
 * Description of CompositeComponentAbstract
 *
 * @author pes2704
 */
abstract class ComponentCompositeAbstract extends CompositeView implements ComponentCompositeInterface {

    /**
     *
     * @var ComponentConfigurationInterface
     */
    protected $configuration;

    public function __construct(ComponentConfigurationInterface $configuration) {
        $this->configuration = $configuration;
    }
    
    public function beforeRenderingHook(): void {
        // component (view) musí mít před renderování nastaven renderer nebo renderer name nebo template
        $data = $this->getData();
        if ($data instanceof StatusViewModelInterface) {
            $representativeCompanyId = $data->getRepresentativeActions()->getRepresentative()->getCompanyId();
            if ($data->getRequestedId()==$representativeCompanyId) {
                $x;
            }
        }
    }
}
