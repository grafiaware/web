<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\ViewModelListInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Entity\Company;
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
    
    public function isListEditable(): bool {
        return $this->isAdministrator();
    }
    
    use RepresentativeTrait;

    /**
     * Poskytuje kolekci dat (iterovatelnou) pro generování položek - item komponentů..
     * Položky - item komponenty vziknou tak, že ke každé položce datové kolekce bude vygenerována item komponenta z prototypu
     * a této komponentě bude vložena jako data pro renderování položka kolekce dat. 
     * Pozn. To znamená, že jednotlívé item komponenty nepoužijí (a nepotřebují) vlastní view model.
     * 
     * @return iterable
     */
    public function provideItemEntityCollection(): iterable {
        $entities = $this->companyRepo->findAll();
        if ($this->isAdministrator()) {
            $entities[] = new Company();  // pro přidání
        }
        return $entities;
    }
    
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
