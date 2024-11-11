<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Entity\CompanyInterface;

use Access\Enum\RoleEnum;

use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyListViewModel extends ViewModelAbstract implements CompanyListViewModelInterface {

    private $status;  
    
    private $companyRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->appendData($this->data());
    }

    private function data() {
        $companies=[];     
        foreach ($this->companyRepo->findAll() as $company) {
            /** @var CompanyInterface $company */
            $companies[] = [
                'companyId' => $company->getId(),
                'name' =>  $company->getName()
                ];
        }

        $array = [
            'companies' => $companies
        ];
        return $array;
    }
}
