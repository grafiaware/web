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
    
    /**
     * Poskytuje kolekci dat (iterovatelnou) pro generování položek - item komponentů..
     * Položky - item komponenty vziknou tak, že ke každé položce datové kolekce bude vygenerována item komponenta z prototypu
     * a této komponentě bude vložena jako data pro renderování položka kolekce dat. 
     * Pozn. To znamená, že jednotlívé item komponenty nepoužijí (a nepotřebují) vlastní view model.
     * 
     * @return iterable
     */
    public function provideItemDataCollection(): iterable {
        $isAdministrator = $this->isAdministrator();
        $items=[];     
        foreach ($this->companyRepo->findAll() as $company) {
            /** @var CompanyInterface $company */
            $editableItem = $isAdministrator || $this->isCompanyEditor($company->getId());
            $items[] = [
                // conditions
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                'remove'=> $isAdministrator,   // přidá tlačítko remove do item
                // text
                'headline' => 'Název firmy',
                //route
                'componentRouteSegment' => 'events/v1/company',
                'id' => $company->getId(),
                // data
                'name' =>  $company->getName()
                ];
        }
        if ($isAdministrator) {
            $items[] = [
                // conditions
                'editable' => true,    // vstupní pole formuláře jsou editovatelná 
                'add' => true,   // zobrazí se tlačítko Uložit
                // text
                'headline' => 'Přidej firmu',                
            ];
        }
        return $items;
    }
    
    /**
     * Poskytuje data pro šablonu list - pro šablonu, která obaluje pro jednotlivé položky
     * @return ArrayIterator
     */
    public function getIterator() {
        $array = [         
            'headline'=>'Firmy', 'addText' => 'Přidej firmu', 'removeText' => 'Smaž firmu',
            'items' => $this->getArrayCopy()];
        return new ArrayIterator($array);
    }
}
