<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Repository\CompanyAddressRepoInterface;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyInterface;

use Component\ViewModel\ViewModelInterface;


use ArrayIterator;

/**
 * 
 */
class CompanyAddressViewModel extends ViewModelAbstract implements ViewModelInterface {

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
    
    
    public function getIterator() {                        
        // $editable = true;                            
        $requestedId = $this->getRequestedId();
        $representativeFromStatus = $this->status->getRepresentativeActions()->getRepresentative();
        
        $editable = isset($representativeFromStatus) ? ($representativeFromStatus->getCompanyId()==$requestedId) : false;                            
        
            /** @var CompanyInterface $company */ 
        $company = $this->companyRepo->get($requestedId);   
                   
            /** @var CompanyAddressInterface $companyAddressEntity */
        $companyAddressEntity = $this->companyAddressRepo->get($requestedId);
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
        return new ArrayIterator($companyAddress);        
    }
    
    
    
    
    
}    
