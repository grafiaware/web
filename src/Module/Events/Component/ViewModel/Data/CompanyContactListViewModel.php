<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\ViewModelInterface;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyContactRepoInterface;
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\CompanyInterface;


use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyContactListViewModel extends ViewModelAbstract implements ViewModelInterface {
    private $status;      
    private $companyRepo;
    private $companyContactRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyContactRepoInterface $companyContactRepo            
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyContactRepo = $companyContactRepo;
    }

    public function getIterator() {
        $requestedId = $this->getRequestedId();
        $representativeFromStatus = $this->status->getRepresentativeActions()->getRepresentative();        

        if (isset($requestedId)) {
            /** @var CompanyInterface $company */ 
            $company = $this->companyRepo->get($requestedId); 
        }
        $companyContactsArray = [];
        /** @var CompanyContactInterface $cCEntity */
            $companyContactEntities = $this->companyContactRepo->find( " company_id = :idCompany ",  ['idCompany'=> $company->getId() ] );
            $editable = isset($representativeFromStatus) ? ($representativeFromStatus->getCompanyId()==$company->getId()) : false;
            foreach ($companyContactEntities as $cCEntity) {           
                $companyContactsArray[] = [
                    'idCompany' => $company->getId(),
                    'companyContactId' => $cCEntity->getId(),
                    'companyId' => $cCEntity->getCompanyId(),
                    'name' =>  $cCEntity->getName(),
                    'phones' =>  $cCEntity->getPhones(),
                    'mobiles' =>  $cCEntity->getMobiles(),
                    'emails' =>  $cCEntity->getEmails(),
                    'editable' => $editable,                  
                ];
            }
        $array = [
                    'companyContacts' => $companyContactsArray,
                    'addContact' => $editable,
                    'companyId' => $company->getId(),
                    'companyName' => $company->getName()
                ];

        return new ArrayIterator($array);
        
    }
}
