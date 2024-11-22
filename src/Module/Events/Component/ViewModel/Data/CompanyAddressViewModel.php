<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelInterface;
use Events\Component\ViewModel\Data\RepresentativeViewModelAbstract;

use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Repository\CompanyAddressRepoInterface;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyInterface;


use ArrayIterator;

/**
 * 
 */
class CompanyAddressViewModel extends RepresentativeViewModelAbstract implements ViewModelInterface {

    private $companyRepo;
    private $companyAddressRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyAddressRepoInterface $companyAddressRepo
            ) {
        parent::__construct($status);
        $this->companyRepo = $companyRepo;
        $this->companyAddressRepo = $companyAddressRepo;
    }
    
    
    public function getIterator() {                        
        $requestedId = $this->getRequestedId();
        $representativeFromStatus = $this->getRepresentativeFromStatus();
        
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
