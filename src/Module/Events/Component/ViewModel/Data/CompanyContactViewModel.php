<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelItemAbstract;
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
class CompanyContactViewModel extends ViewModelItemAbstract {
    
    private $status;
    private $companyRepo;
    private $companyContactRepo;
    private $companyContact;
    private $familyRouteSegment;


    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyContactRepoInterface $companyContactRepo            
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyContactRepo = $companyContactRepo;
    }
    
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof CompanyContactInterface) {
            $this->companyContact = $entity;
        } else {
            $cls = CompanyContactInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function receiveFamilyRouteSegment(string $familyRouteSegment) {
        $this->familyRouteSegment = $familyRouteSegment;
    }
    
    public function isItemEditable(): bool {
        $this->loadCompany();
        return $this->isAdministrator() || $this->isCompanyRepresentative($this->companyContact->getCompanyId());
    }
    
    use RepresentativeTrait;
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyEditor($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }
    
    private function loadCompany() {
        if (!isset($this->companyContact)) {
            if ($this->hasItemId()) {
                $this->companyContact = $this->companyContactRepo->get($this->getItemId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator() {
        $requestedId = $this->getItemId();
        $componentRouteSegment = "events/v1/companycontact";

        $this->companyContact = $this->companyContactRepo->get($requestedId);
        
        $editableItem = $this->isAdministrator() || $this->isCompanyEditor($this->companyContact->getCompanyId());
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

        foreach ($companyContacts as $companyContact) {   
            $items[] = [
                // conditions
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                'remove'=> $editableItem,   // přidá tlačítko remove do item
                // text
                'headline' => 'Jméno kontaktu',
                //route
                'componentRouteSegment' => $componentRouteSegment,
                'id' => $companyContact->getId(),
                // data
                'fields' => [
                    'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                    'name' =>  $companyContact->getName(),
                    'phones' =>  $companyContact->getPhones(),
                    'mobiles' =>  $companyContact->getMobiles(),
                    'emails' =>  $companyContact->getEmails(),      
                    ], 
            ];
        }
        if ($editableItem) {
            $items[] = [
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
