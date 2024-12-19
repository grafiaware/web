<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelItemAbstract;

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
class JobToTagViewModel extends ViewModelItemAbstract {

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
        return $this->isAdministrator();
    }
    
    use RepresentativeTrait;
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function loadJobToTag() {
        if (!isset($this->jobToTag)) {
            if ($this->hasItemId()) {
                $this->jobToTag = $this->jobToTagRepo->get($this->getItemId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator() {
        $this->loadJobToTag();
        $componentRouteSegment = 'events/v1/company';   //TODO: getRouteSegment() do abstractu - obdobně jako ve ViewModelFamilyAbstract

        $editableItem = $this->isItemEditable();
        $id = $this->jobToTag->getId();
        if (isset($id)) {
            $item = [
                // conditions
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                'remove'=> $editableItem,   // přidá tlačítko remove do item
                //route
                'componentRouteSegment' => $componentRouteSegment,
                'id' => $id,
                // data
                'fields' => ['editable' => $editableItem, 'tag' => $this->jobToTag->getTag()],
                ];
        } elseif ($editableItem) {
            $item = [
                // conditions
                'editable' => true,    // seznam je editovatelný - zobrazí formulář a tlačítko přidat 
                //route
                'componentRouteSegment' => $componentRouteSegment,
                // text
                'addHeadline' => 'Přidej tag',                
                // data
                'fields' => ['editable' => $editableItem],
                ];
        }        
        
        $this->appendData($item);
        return parent::getIterator();
    }
}
