<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelItemAbstract;
use Component\ViewModel\ViewModelItemInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyAddressRepoInterface;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyInterface;

use Access\Enum\RoleEnum;

use ArrayIterator;
use UnexpectedValueException;
/**
 * 
 */
class CompanyAddressViewModel extends ViewModelItemAbstract implements ViewModelItemInterface {

    private $status;
    private $companyRepo;
    private $companyAddressRepo;

    use RepresentativeTrait;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyAddressRepoInterface $companyAddressRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyAddressRepo = $companyAddressRepo;
    }
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyEditor($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }    
    
    public function getIterator() {                        
        $requestedId = $this->getItemId();
        $editable = $this->isAdministrator() || $this->isCompanyEditor($requestedId);

                   
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
            /** @var CompanyInterface $company */ 
            if ($this->companyRepo->get($requestedId)) {
                $companyAddress = [
                    'editable' => $editable,                    
                    'companyId_proInsert'=> $requestedId,
                    ];
            } else {
                throw new UnexpectedValueException("Neexistuje firma s požadovaným id.");
            }
        }                   
        return new ArrayIterator($companyAddress);        
    }
    
    
    
    
    
}    
