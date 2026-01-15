<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\ViewModelListInterface;

use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Entity\JobTagInterface;
use Events\Model\Entity\JobTag;

use Events\Component\ViewModel\Data\RepresentativeTrait;

use Access\Enum\RoleEnum;

use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class JobTagListViewModel extends ViewModelAbstract implements ViewModelListInterface {

    private $status;  
    
    private $jobTagRepo;

    public function __construct(
            StatusViewModelInterface $status,
            JobTagRepoInterface $companyRepo
            ) {
        $this->status = $status;
        $this->jobTagRepo = $companyRepo;
    }
    
    use RepresentativeTrait;
        
    public function isListEditable(): bool {
        return $this->isAdministratorEditor();
    } 
    
    public function provideItemEntityCollection(): iterable {
        $entities = $this->jobTagRepo->findAll();
//        tady: asociativní pole id=>entita
        
        if ($this->isListEditable()) {
            $entities[] = new JobTag();  // pro přidání
        }
        return $entities;        
    }
    
    /**
     * Poskytuje data pro šablonu list - pro šablonu, která obaluje pro jednotlivé položky
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator {
        $array = [         
            'listHeadline'=>'Tagy pracovních pozic', 
            'items' => $this->getArrayCopy()];
        return new ArrayIterator($array);
    }
}
