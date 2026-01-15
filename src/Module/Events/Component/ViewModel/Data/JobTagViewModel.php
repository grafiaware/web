<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelItemAbstract;

use Component\ViewModel\StatusViewModelInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Entity\JobTagInterface;
use Model\Entity\EntityInterface;

use Access\Enum\RoleEnum;

use Exception;
use TypeError;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class JobTagViewModel extends ViewModelItemAbstract {

    private $status;  
    
    private $jobTagRepo;
    
    /**
     * 
     * @var JobTagInterface
     */
    private $jobTag;
    
    public function __construct(
            StatusViewModelInterface $status,
            JobTagRepoInterface $jobTagRepo
            ) {
        $this->status = $status;
        $this->jobTagRepo = $jobTagRepo;
    }
    
    use RepresentativeTrait;
            
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof JobTagInterface) {
            $this->jobTag = $entity;
        } else {
            $cls = JobTagInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isDataEditable(): bool {
        $this->loadJobTag();
        return $this->isAdministratorEditor();
    }

    private function loadJobTag() {
        if (!isset($this->jobTag)) {
            if ($this->getSingleRouteSegment()->getChildId()) {
                $this->jobTag = $this->jobTagRepo->get($this->getSingleRouteSegment()->getChildId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator() {
        $this->loadJobTag();
        $componentRouteSegment = 'events/v1/jobtag';   //TODO: getRouteSegment() do abstractu - obdobně jako ve ViewModelFamilyAbstract

        $id = $this->jobTag->getId();
        if (isset($id)) {
            $item = [
                //route
                'actionSave' => $componentRouteSegment."/$id",
                'actionRemove' => $componentRouteSegment."/$id/remove",
                'id' => $id,
                // data
                'fields' => ['tag' => $this->jobTag->getTag(), 'color' => $this->jobTag->getColor()],
                ];
        } elseif ($this->isDataEditable()) {
            $item = [
                //route
                'actionAdd' => $componentRouteSegment,
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
