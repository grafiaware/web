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
class CompanyContactsListViewModel extends ViewModelAbstract implements ViewModelInterface {
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
        $requestedId = $this->offsetGet('requestedId');
        $representativeFromStatus = $this->status->getRepresentativeActions()->getRepresentative();
        
        $editable = isset($representativeFromStatus) ? ($representativeFromStatus->getCompanyId()==$requestedId) : false;
        if (isset($representativeFromStatus)) {   
            /** @var CompanyInterface $company */ 
            $company = $this->companyRepo->get($requestedId);            

            $companyContacts=[];
            $companyContactEntities = $this->companyContactRepo->find( " company_id = :idCompany ",  ['idCompany'=> $requestedId ] );

            /** @var CompanyContactInterface $cCEntity */
            foreach ($companyContactEntities as $cCEntity) {               
                $companyContacts[] = [
                    'editable' => $editable,  

                    'companyContactId' => $cCEntity->getId(),
                    'companyId' => $cCEntity->getCompanyId(),
                    'name' =>  $cCEntity->getName(),
                    'phones' =>  $cCEntity->getPhones(),
                    'mobiles' =>  $cCEntity->getMobiles(),
                    'emails' =>  $cCEntity->getEmails()
                    ];
            }            
            
            $array = [
            'idCompany' => $company->getId(),
            'companyContacts' => $companyContacts,
            'name' => $company->getName(),
            'editable' => $editable,
            ];
         
             
             
        }else {                    
            $array = [
                //'idCompany' => $company->getId(),
                'companyContacts' =>[],
                'name' => '',
                'editable' => $editable,  
            ];
        }
        return new ArrayIterator($array);
        
    }
}
