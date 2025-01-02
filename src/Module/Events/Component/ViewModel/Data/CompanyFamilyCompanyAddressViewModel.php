<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyItemAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyAddressRepoInterface;
use Events\Model\Entity\CompanyAddressInterface;
use Model\Entity\EntityInterface;

use Access\Enum\RoleEnum;
use TypeError;
use Exception;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyFamilyCompanyAddressViewModel extends ViewModelFamilyItemAbstract {
    
    private $status;
    private $companyRepo;
    private $companyAddressRepo;
    private $companyAddress;
    
    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyAddressRepoInterface $addressRepo            
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyAddressRepo = $addressRepo;
    }

    use RepresentativeTrait;
    
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof CompanyAddressInterface) {
            $this->companyAddress = $entity;
        } else {
            $cls = CompanyAddressInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isItemEditable(): bool {
        $this->loadCompanyAddress();
        return $this->isAdministrator() || $this->isCompanyEditor($this->companyAddress->getCompanyId());
    }
        
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyEditor($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }
    
    private function loadCompanyAddress() {
        if (!isset($this->companyAddress)) {
            if ($this->hasItemId()) {
                $this->companyAddress = $this->companyAddressRepo->get($this->getItemId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator() {
        $this->loadCompanyAddress();
        $editableItem = $this->isItemEditable();
        $this->getFamilyRouteSegment()->setChildId($this->companyAddress->getCompanyId());
        $componentRouteSegment = $this->getFamilyRouteSegment();
        if ($componentRouteSegment->hasChildId()) {        
            $companyAddrArray = [
                // conditions
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                'remove'=> $editableItem,   // přidá tlačítko remove do item
                //route
                    'actionSave' => $componentRouteSegment->getSavePath(),
                    'actionRemove' => $componentRouteSegment->getRemovePath(),
                // data
                'fields' => [
                    'editable' => $editableItem,
                    'name'   => $this->companyAddress->getName(),
                    'lokace' => $this->companyAddress->getLokace(),
                    'psc'    => $this->companyAddress->getPsc(),
                    'obec'   => $this->companyAddress->getObec()
                ],                
            ];
        } elseif ($editableItem) {
            /** @var CompanyInterface $company */ 
            if ($this->companyRepo->get($this->getFamilyRouteSegment()->getParentId())) {  // validace id rodiče
                $companyAddrArray = [
                    // conditions
                    'editable' => true,    // zobrazí formulář a tlačítko přidat 
                    // text
                    'addHeadline' => 'Přidej adresu',                      
                    //route
                    'actionAdd' => $componentRouteSegment->getAddPath(),
                    // data
                    'fields' => [
                        'editable' => $editableItem,]                    
                    ];        
            }
        }
        $this->appendData($companyAddrArray);
        return parent::getIterator();        
    }
}
