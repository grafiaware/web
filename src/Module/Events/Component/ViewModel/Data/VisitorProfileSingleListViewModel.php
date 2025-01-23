<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelSingleListAbstract;
use Component\ViewModel\ViewModelListInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\VisitorProfileRepoInterface;
use Events\Model\Entity\VisitorProfile;
use Access\Enum\RoleEnum;

use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class VisitorProfileSingleListViewModel extends ViewModelSingleListAbstract implements ViewModelListInterface {

    private $status;  
    
    private $visitorProfileRepo;

    public function __construct(
            StatusViewModelInterface $status,
            VisitorProfileRepoInterface $visitorProgileRepo
            ) {
        $this->status = $status;
        $this->visitorProfileRepo = $visitorProgileRepo;
    }
    
    private function isAdministrator() {
        return ($this->status->getUserRole() == RoleEnum::EVENTS_ADMINISTRATOR);
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
        $entities = $this->visitorProfileRepo->findAll();
        if ($this->isListEditable()) {
            $entities[] = new VisitorProfile();
        }
        return $entities;
    }
    
    /**
     * Poskytuje data pro šablonu list - pro šablonu, která obaluje pro jednotlivé položky
     * @return ArrayIterator
     */
    public function getIterator() {
        $array = [         
            'listHeadline'=>'Návštěvníci', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();     }
}
