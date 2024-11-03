<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Entity\CompanyInterface;

use Access\Enum\RoleEnum;

use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyViewModel extends ViewModelAbstract implements CompanyViewModelInterface {

    private $status;
    
    private $representativeRepo;
    
    private $companyRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
    }

    public function getIterator() {
        $editable = $this->status->getUserRole()===RoleEnum::EVENTS_ADMINISTRATOR;

        $companies=[];     
        foreach ($this->companyRepo->findAll() as $company) {
            /** @var CompanyInterface $company */
            $companies[] = [
                'editable' => $editable,
                'companyId' => $company->getId(),
                'name' =>  $company->getName()
                ];
        }

        $array = [
            'editable' => $editable,
            'companies' => $companies
        ];
        return new ArrayIterator($array);
    }
}
