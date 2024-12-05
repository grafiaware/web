<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelChildListAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyContactRepoInterface;
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\CompanyInterface;

use Access\Enum\RoleEnum;
use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyChildCompanyContactListViewModel extends ViewModelChildListAbstract {
    
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

    use RepresentativeTrait;
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyEditor($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }
    
    public function provideItemDataCollection(): iterable {
        $parentId = $this->getParentId();
        $componentRouteSegment = "events/v1/company/$parentId/companycontact";
        $items = [];
        /** @var CompanyContactInterface $companyContact */
        $companyContacts = $this->companyContactRepo->find( " company_id = :idCompany ",  ['idCompany'=> $parentId ] );
        foreach ($companyContacts as $companyContact) {           
            $editableItem = $this->isAdministrator() || $this->isCompanyEditor($companyContact->getCompanyId());
            $items[] = [
                // conditions
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                'remove'=> $editableItem,   // přidá tlačítko remove do item
                // text
                'headline' => 'Jméno kontaktu',
                //route
                'componentRouteSegment' => $componentRouteSegment,
                'id' => $companyContact->getId(),
                // data,
                // data
                'fields' => [
                    'editable' => $editableItem,               
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
                'add' => true,   // zobrazí se tlačítko Uložit      ?????????????
                // text
                'addHeadline' => 'Přidej kontakt',                 
                //route
                'componentRouteSegment' => $componentRouteSegment,
                // data
                'fields' => [
                    'editable' => $editableItem,]
            ];
        }        
        return $items;
    }
    public function getIterator() {

        $array = [         
            'listHeadline'=>'Kontakty', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();        
    }
}