<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\ViewModelInterface;

use Component\ViewModel\StatusViewModelInterface;
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

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo
            ) {
        parent::__construct();
        $this->status = $status;
        $this->companyRepo = $companyRepo;
    }

    public function getIterator() {     
        $companyArray = [];
        if ($this->hasIdentity()) {
            $company = $this->companyRepo->get($this->getIdentity());
            if (isset($company)) {
                /** @var CompanyInterface $company */
                $companyArray = [
                    'companyId' => $company->getId(),
                    'name' =>  $company->getName()
                    ];
            }
        } 
        return new ArrayIterator($companyArray);
    }
}
