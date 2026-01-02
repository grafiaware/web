<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelSingleListAbstract;
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
class DocumentSingleListViewModel extends ViewModelSingleListAbstract implements ViewModelListInterface {

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
    
    protected function loadListEntities() {
        $this->listEntities = $this->documentRepo->findAll();
    }
    
    protected function newListEntity() {
        return new Document();  // "prázdná" entita pro formulář pro přidání
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
