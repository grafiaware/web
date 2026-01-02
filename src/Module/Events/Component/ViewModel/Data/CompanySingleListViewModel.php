<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelSingleListAbstract;
use Component\ViewModel\ViewModelListInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Entity\Company;
use Access\Enum\RoleEnum;

use ArrayIterator;

/**
 * Description of CompanySingleListViewModel
 *
 * @author pes2704
 */
class CompanySingleListViewModel extends ViewModelSingleListAbstract implements ViewModelListInterface {

    private $status;  
    
    private $companyRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
    }
    
    public function isListEditable(): bool {
        return $this->isAdministrator();
    }
    
    protected function loadListEntities() {
        $this->listEntities = $this->companyRepo->findAll();
    }
    
    protected function newListEntity() {
        return new Company();  // "prázdná" entita pro formulář pro přidání
    }
    
    use RepresentativeTrait;

    /**
     * Poskytuje kolekci dat (iterovatelnou) pro generování položek - item komponentů..
     * Položky - item komponenty vziknou tak, že ke každé položce datové kolekce bude vygenerována item komponenta z prototypu
     * a této komponentě bude vložena jako data pro renderování položka kolekce dat. 
     * Pozn. To znamená, že jednotlivé item komponenty nepoužijí (a nepotřebují) vlastní view model.
     * 
     * @return iterable
     */
//    public function provideItemEntityCollection(): iterable {
//        $entities = $this->companyRepo->findAll();
////        tady: asociativní pole id=>entita
//        if ($this->isListEditable()) {
//            $entities[] = new Company();  // pro přidání
//        }
//        return $entities;
//    }
    
    /**
     * Poskytuje data pro šablonu list - pro šablonu, která obaluje pro jednotlivé položky
     * @return ArrayIterator
     */
    public function getIterator() {
        $array = [         
            'listHeadline'=>'Firmy', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();     }
}
