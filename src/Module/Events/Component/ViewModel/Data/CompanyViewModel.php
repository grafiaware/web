<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\ViewModelInterface;

use Component\ViewModel\StatusViewModelInterface;
use Model\Entity\EntityInterface;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Entity\CompanyInterface;

use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyViewModel extends ViewModelAbstract implements ViewModelInterface {

    private $status;  
    
    private $companyRepo;
    
    /**
     * 
     * @var CompanyInterface
     */
    private $company;
    
    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo
            ) {
        parent::__construct();
        $this->status = $status;
        $this->companyRepo = $companyRepo;
    }

    public function setEntity($identityValue, EntityInterface $entity) {
        $this->setRequestedId($identityValue);
        $this->company = $entity;
    }
    
    public function getIterator() {     
        if (!isset($this->company)) {
            if ($this->hasRequestedId()) {
                $this->company = $this->companyRepo->get($this->getRequestedId());     
            }
        }

        if (isset($this->company)) {
            /** @var CompanyInterface $company */
            $array = [
                'name' => $this->company->getName()
                ];
        } 
        return new ArrayIterator($array);
    }
}
