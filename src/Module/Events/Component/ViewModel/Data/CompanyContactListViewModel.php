<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelChildListAbstract;
use Component\ViewModel\ViewModelChildListInterface;
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
class CompanyContactListViewModel extends ViewModelChildListAbstract implements ViewModelChildListInterface {
    
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
        $requestedId = $this->getParentId();
        $componentRouteSegment = "events/v1/company/$requestedId/companyContact";
        $items = [];
        /** @var CompanyContactInterface $companyContact */
        $companyContacts = $this->companyContactRepo->find( " company_id = :idCompany ",  ['idCompany'=> $requestedId ] );
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
                'name' =>  $companyContact->getName(),
                'phones' =>  $companyContact->getPhones(),
                'mobiles' =>  $companyContact->getMobiles(),
                'emails' =>  $companyContact->getEmails(),                    
            ];
        }
        if ($editableItem) {
            $items[] = [
                // conditions
                'editable' => true,    // vstupní pole formuláře jsou editovatelná 
                'add' => true,   // zobrazí se tlačítko Uložit
                // text
                'headline' => 'Přidej kontakt',                
                //route
                'componentRouteSegment' => $componentRouteSegment,
            ];
        }        
        return $items;
    }
    public function getIterator() {

        $array = [         
            'headline'=>'Kontakty', 'addText' => 'Přidej kontakt', 'removeText' => 'Smaž kontakt',
            'items' => $this->getArrayCopy()];
        return new ArrayIterator($array);
        
    }
}