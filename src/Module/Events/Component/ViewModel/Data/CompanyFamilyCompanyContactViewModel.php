<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyItemAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyContactRepoInterface;
use Events\Model\Entity\CompanyContactInterface;
use Model\Entity\EntityInterface;


use Access\Enum\RoleEnum;
use Exception;
use TypeError;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyFamilyCompanyContactViewModel extends ViewModelFamilyItemAbstract {
    
    private $status;
    private $companyRepo;
    private $companyContactRepo;
    
    /**
     * 
     * @var CompanyContactInterface
     */
    private $companyContact;


    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyContactRepoInterface $companyContactRepo            
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyContactRepo = $companyContactRepo;
    }

    use RepresentativeTrait;    
    
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof CompanyContactInterface) {
            $this->companyContact = $entity;
        } else {
            $cls = CompanyContactInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isItemEditable(): bool {
        $this->loadCompanyContact();
        return $this->isAdministrator() || $this->isCompanyEditor($this->companyContact->getCompanyId());
    }
        
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyEditor($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }
    
    private function loadCompanyContact() {
        if (!isset($this->companyContact)) {
            if ($this->hasItemId()) {
                $this->companyContact = $this->companyContactRepo->get($this->getItemId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator() {
        $this->loadCompanyContact();
        $componentRouteSegment = $this->getFamilyRouteSegment()->getPath();
        $editableItem = $this->isItemEditable();
        $id = $this->companyContact->getCompanyId();        
        if (isset($id)) {                
            $companyContactArray = [
                // conditions
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                'remove'=> $editableItem,   // přidá tlačítko remove do item
                //route
                'componentRouteSegment' => $componentRouteSegment,
                'id' => $this->companyContact->getId(),
                // data
                'fields' => [
                    'editable' => $editableItem,
                    'name' =>  $this->companyContact->getName(),
                    'phones' =>  $this->companyContact->getPhones(),
                    'mobiles' =>  $this->companyContact->getMobiles(),
                    'emails' =>  $this->companyContact->getEmails(),
                    ],                      
                ];
        } elseif ($editableItem) {
            $companyContactArray = [
                // conditions
                'editable' => true,    // seznam je editovatelný - zobrazí formulář a tlačítko přidat 
                // text
                'addHeadline' => 'Přidej kontakt',                 
                //route
                'componentRouteSegment' => $componentRouteSegment,
                // data
                'fields' => [
                    'editable' => $editableItem,]
            ];
        }                
        
        
        $this->appendData($companyContactArray);
        return parent::getIterator();        
    }
}
