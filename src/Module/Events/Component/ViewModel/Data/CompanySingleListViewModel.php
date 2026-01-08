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

use Model\RowData\RowData;
use Events\Model\Hydrator\CompanyHydrator;

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
        /**
     * {@inheritdoc}
     *
     * @param array $names
     * @param string $placeholderPrefix
     * @return array
     */
    private function touples(array $names): array {
        $touples = [];
        foreach ($names as $name) {
            $touples[] =  $name . " = :".$name;   // $this->identificator($name) . " = :".$placeholderPrefix.$name;
        }
        return $touples;
    }
    protected function loadListEntities() {  //TODO: SV QUERY - testovací implementace
        $query = $this->getQuery();
        if ($query) {
            $whereClause = implode(' AND ', $this->touples(array_keys($query)));
            $this->listEntities = $this->companyRepo->find($whereClause, $query);
        } else {
            $this->listEntities = $this->companyRepo->findAll();            
        }
    }
    
    protected function newListEntity() {
        $company = new Company();  // "prázdná" entita pro formulář pro přidání
        $row = new RowData($this->query);  //TODO: SV QUERY - testovací implementace
        $hydrator = new CompanyHydrator();
        $hydrator->hydrate($company, $row);
        return $company;
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
