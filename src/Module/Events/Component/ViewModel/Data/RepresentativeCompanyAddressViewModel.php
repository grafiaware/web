<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelItemInterface;
use Component\ViewModel\ViewModelItemAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyAddressRepoInterface;
use Events\Model\Entity\CompanyAddressInterface;

use Events\Model\Entity\CompanyInterface;

use ArrayIterator;

/**
 * 
 */
class RepresentativeCompanyAddressViewModel extends ViewModelItemAbstract implements ViewModelItemInterface {

    private $status;
    private $companyRepo;
    private $companyAddressRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyAddressRepoInterface $companyAddressRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyAddressRepo = $companyAddressRepo;
    }

    use RepresentativeTrait;
    
    public function getIterator() {                                                         
        $representativeFromStatus = $this->getRepresentativeFromStatus();

        if (isset($representativeFromStatus)) {   
            $editable = true;
            $representativeCompanyId = $representativeFromStatus->getCompanyId();
            /** @var CompanyInterface $company */ 
            $company = $this->companyRepo->get($representativeCompanyId);            
            /** @var CompanyAddressInterface $companyAddressEntity */
            $companyAddressEntity = $this->companyAddressRepo->get($representativeCompanyId);
            if (isset($companyAddressEntity)) {           
                $companyAddress = [
                'editable' => $editable,                    
                    'companyId'=> $companyAddressEntity->getCompanyId(),
                    'name'   => $companyAddressEntity->getName(),
                    'lokace' => $companyAddressEntity->getLokace(),
                    'psc'    => $companyAddressEntity->getPsc(),
                    'obec'   => $companyAddressEntity->getObec()
                    ];
            }else {
                $companyAddress = [
                'editable' => $editable,                    
                    'companyId_proInsert'=> $company->getId(),
                    ];
            }                   
        }
       
        $array = [
            'companyAddress' => $companyAddress,
            'name' => $company->getName()
        ];
        return new ArrayIterator($array);                
    }
    
    
    
    
    
}    
