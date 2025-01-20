<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\ViewModelListInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\DocumentRepoInterface;
use Events\Model\Entity\DocumentInterface;
use Events\Model\Entity\Document;
use Access\Enum\RoleEnum;

use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class DocumentListViewModel extends ViewModelAbstract implements ViewModelListInterface {

    private $status;  
    
    private $documentRepo;

    public function __construct(
            StatusViewModelInterface $status,
            DocumentRepoInterface $documentRepo
            ) {
        $this->status = $status;
        $this->documentRepo = $documentRepo;
    }
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }
    
    public function isListEditable(): bool {
        return $this->isAdministrator();
    }
    
    /**
     * Poskytuje kolekci dat (iterovatelnou) pro generování položek - item komponentů..
     * Položky - item komponenty vziknou tak, že ke každé položce datové kolekce bude vygenerována item komponenta z prototypu
     * a této komponentě bude vložena jako data pro renderování položka kolekce dat. 
     * Pozn. To znamená, že jednotlívé item komponenty nepoužijí (a nepotřebují) vlastní view model.
     * 
     * @return iterable
     */
    public function provideItemEntityCollection(): iterable {
        $entities = $this->documentRepo->findAll();
        if ($this->isListEditable()) {
            $entities[] = new Document();  // pro přidání
        }
        return $entities;
    }
    
    /**
     * Poskytuje data pro šablonu list - pro šablonu, která obaluje pro jednotlivé položky
     * @return ArrayIterator
     */
    public function getIterator() {
        $array = [         
            'listHeadline'=>'Dokumenty', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();     }
}
