<?php
namespace Events\Middleware\Events\ViewModel;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\EventContentTypeRepoInterface;

/**
 * Description of EventType
 *
 * @author pes2704
 */
class EventContentTypeViewModel {
    
    /**
     * 
     * @var StatusViewModelInterface
     */
    private $statusViewModel;
    
    private $eventContentTypeRepo;

    public function __construct(
            StatusViewModelInterface $statusViewModel,
            EventContentTypeRepoInterface $eventContentTypeRepo) {
        $this->statusViewModel = $statusViewModel;
        
        $this->eventContentTypeRepo = $eventContentTypeRepo;
    }

    public function getEventType($type) {
        $eventContentType = $this->eventContentTypeRepo->getByType($type);
        $eventContentTypeArray = ['id' => $eventContentType->getId(), 'type' => $eventContentType->getType(), 'name' => $eventContentType->getName()];       
        return $eventContentTypeArray;
    }
    
    public function getEventTypesArray() {
        $eventContentTypes = $this->eventContentTypeRepo->findAll();
        $allContentTypeArray=[];
        /** @var  EventContentInterface $eventContentType */
        foreach ($eventContentTypes as $eventContentType) {
            $allContentTypeArray[] = ['id' => $eventContentType->getId(), 'type' => $eventContentType->getType(), 'name' => $eventContentType->getName()];       
        }        
        return $allContentTypeArray;
    }
}
