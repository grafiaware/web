<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\ViewModelListInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Entity\CompanyInterface;

use Access\Enum\RoleEnum;

use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyListViewModel extends ViewModelAbstract implements ViewModelListInterface {

    private $status;  
    
    private $companyRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
    }
    
    use RepresentativeTrait;
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyEditor($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }
    
    public function provideItemDataCollection(): iterable {
        $isAdministrator = $this->isAdministrator();
        $items=[];     
        foreach ($this->companyRepo->findAll() as $company) {
            /** @var CompanyInterface $company */
            $this->status->getRepresentativeActions()->getDataEditable();
            $editableItem = $isAdministrator || $this->isCompanyEditor($company->getId());
            $items[] = [
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                'remove'=> $isAdministrator,   // přidá tlačítko remove do item
                'headline' => 'Název firmy',
                'id' => $company->getId(),
                'name' =>  $company->getName()
                ];
        }
        if ($isAdministrator) {
            $items[] = [
                'editable' => true,    // vstupní pole formuláře jsou editovatelná 
                'add' => true,   // zobrazí se tlačítko Uložit
                'headline' => 'Přidej firmu',                
            ];
        }
        return $items;
    }
    
    public function getIterator() {
        $array = [         
            'headline'=>'Firmy', 'addText' => 'Přidej firmu', 'removeText' => 'Smaž firmu',
            'items' => $this->getArrayCopy()];
        return new ArrayIterator($array);
    }
}
