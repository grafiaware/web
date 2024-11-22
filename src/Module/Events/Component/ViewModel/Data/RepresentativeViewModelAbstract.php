<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\ViewModelInterface;
use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Entity\RepresentativeInterface;

/**
 * Description of RepresentativeViewModelAbstract
 *
 * @author pes2704
 */
class RepresentativeViewModelAbstract extends ViewModelAbstract implements ViewModelInterface {
    
    private $status;
    
    public function __construct(
            StatusViewModelInterface $status
        ) {
            $this->status = $status;
    }
        
    protected function getRepresentativeFromStatus(): ?RepresentativeInterface {
        $representativeActions = $this->status->getRepresentativeActions();
        return isset($representativeActions) ? $representativeActions->getRepresentative() : null;    
    }
}
