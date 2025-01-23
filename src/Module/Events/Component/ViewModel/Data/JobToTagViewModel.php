<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyItemAbstract;

use Component\ViewModel\StatusViewModelInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Events\Model\Repository\JobToTagRepoInterface;
use Events\Model\Entity\JobToTagInterface;
use Model\Entity\EntityInterface;

use Access\Enum\RoleEnum;

use Exception;
use TypeError;

/**
 * Description of JobToTagViewModel
 *
 * @author pes2704
 */
class JobToTagViewModel extends ViewModelFamilyItemAbstract {

    private $status;  
    
    private $jobToTagRepo;
    
    /**
     * 
     * @var JobToTagInterface
     */
    private $jobToTag;
    
    public function __construct(
            StatusViewModelInterface $status,
            JobToTagRepoInterface $jobToTagRepo
            ) {
        $this->status = $status;
        $this->jobToTagRepo = $jobToTagRepo;
    }
    
    use RepresentativeTrait;
        
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof JobToTagInterface) {
            $this->jobToTag = $entity;
        } else {
            $cls = JobToTagInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isItemEditable(): bool {
        $this->loadJobToTag();
        return $this->isAdministratorEditor();
    }

    private function loadJobToTag() {
        if (!isset($this->jobToTag)) {
            if ($this->getSingleRouteSegment()->getChildId()) {
                $this->jobToTag = $this->jobToTagRepo->get($this->getSingleRouteSegment()->getChildId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator() {
        $this->loadJobToTag();
        $componentRouteSegment = 'events/v1/company';   //TODO: getRouteSegment() do abstractu - obdobně jako ve ViewModelFamilyAbstract

        $id = $this->jobToTag->getId();
        if (isset($id)) {
            $item = [
                //route
                'componentRouteSegment' => $componentRouteSegment,
                'id' => $id,
                // data
                'fields' => ['tag' => $this->jobToTag->getTag()],
                ];
        } elseif ($this->isItemEditable()) {
            $item = [
                // conditions
                'editable' => true,    // seznam je editovatelný - zobrazí formulář a tlačítko přidat 
                //route
                'componentRouteSegment' => $componentRouteSegment,
                // text
                'addHeadline' => 'Přidej tag',                
                // data
                'fields' => [],
                ];
        }        
        
        $this->appendData($item);
        return parent::getIterator();
    }
}
