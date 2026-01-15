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
            VisitorProfileRepoInterface $visitorProfileRepo
            ) {
        $this->status = $status;
        $this->visitorProfileRepo = $visitorProfileRepo;
    }
    
    private function isAdministrator() {
        return ($this->status->getUserRole() == RoleEnum::EVENTS_ADMINISTRATOR);
    }
    
    public function isListEditable(): bool {
        return $this->isAdministrator();  //zde events administrator
    }
    // volá se z ViewModelSingleListAbstract->provideItemEntityCollection()
    protected function loadListEntities() {
        $this->listEntities = $this->visitorProfileRepo->findAll();
//                tady: asociativní pole id=>entita
    }
    
    protected function newListEntity() {
        return new VisitorProfile();  // "prázdná" entita pro formulář pro přidání
    }
    
    /**
     * Poskytuje data pro šablonu list - pro šablonu, která obaluje pro jednotlivé položky
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator {
        $array = [         
            'listHeadline'=>'Návštěvníci', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();     }
}
