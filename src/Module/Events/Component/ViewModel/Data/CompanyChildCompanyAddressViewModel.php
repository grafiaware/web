<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelChildItemAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyAddressRepoInterface;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyInterface;

use Access\Enum\RoleEnum;

use ArrayIterator;
use LogicException;
use UnexpectedValueException;
/**
 * 
 */
class CompanyChildCompanyAddressViewModel extends ViewModelChildItemAbstract {

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
        if ($this->hasParentId()) {
            $companyId = $this->getParentId();  // id company            
        } else {
            throw new LogicException("Není nastaveno parent id.");
        }
        if( null==$this->companyRepo->get($companyId) ) {
            throw new UnexpectedValueException("Neexistuje parent entita.");
        }

        $isAdministrator = $this->isAdministrator();
        
        $editableItem = $isAdministrator || $this->isCompanyEditor($companyId);
        $componentRouteSegment = "events/v1/company/$companyId/companyaddress";
        
                
        /** @var CompanyAddressInterface $companyAddress */
        $companyAddress = $this->companyAddressRepo->get($companyId);  // pk = fk
        if (isset($companyAddress)) {
            $companyAddrArray = [
                // conditions
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                'remove'=> $isAdministrator,   // přidá tlačítko remove do item
                //route
                'componentRouteSegment' => $componentRouteSegment,
                'id' => $companyAddress->getCompanyId(),
                // data
                'fields' => [
                    'editable' => $editableItem,
                    'name'   => $companyAddress->getName(),
                    'lokace' => $companyAddress->getLokace(),
                    'psc'    => $companyAddress->getPsc(),
                    'obec'   => $companyAddress->getObec()
                ],                
            ];           
        } elseif ($editableItem) {
            /** @var CompanyInterface $company */ 
            if ($this->companyRepo->get($companyId)) {  // validace id rodiče
                $companyAddrArray = [
                    // conditions
                    'editable' => true,    // zobrazí formulář a tlačítko přidat 
                    // text
                    'addHeadline' => 'Přidej adresu',                      
                    'companyId'=> $companyId,
                    //route
                    'componentRouteSegment' => $componentRouteSegment,
                    // data
                    'addHeadline' => $addHeadline,

                    'fields' => [
                        'editable' => $editableItem,]                    
                    ];
            } else {
                throw new UnexpectedValueException("Neexistuje firma s požadovaným id.");
            }
        } else {
            $companyAddrArray = [];
        }
        $this->appendData($companyAddrArray);
        return parent::getIterator();        
    }
    
    
    
    
    
}    
