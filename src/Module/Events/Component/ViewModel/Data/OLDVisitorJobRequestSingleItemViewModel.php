<?php
namespace Events\Component\ViewModel\Data;

use Site\ConfigurationCache;

use Component\ViewModel\StatusViewModelInterface;

use Component\ViewModel\ViewModelSingleItemAbstract;
use Component\ViewModel\ViewModelItemInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Auth\Model\Entity\LoginAggregateFullInterface;
use Status\Model\Entity\StatusSecurityInterface;

use Events\Model\Repository\VisitorJobRequestRepoInterface;
use Events\Model\Entity\VisitorJobRequest;

use Events\Model\Entity\VisitorJobRequestInterface;
use Events\Model\Entity\DocumentInterface;

use Events\Middleware\Events\Controler\VisitorProfileControler;
use Model\Entity\EntityInterface;

use Access\Enum\RoleEnum;
use ArrayIterator;

/**
 * 
 */
class VisitorJobRequestSingleItemViewModel extends  ViewModelSingleItemAbstract implements ViewModelItemInterface {

    private $status;
    private $visitorJobRequestRepo;

    /**
     * 
     * @var VisitorJobRequestInterface
     */
    private $visitorJobRequest;

     public function __construct(
            StatusViewModelInterface $status,
            VisitorJobRequestRepoInterface $visitorJobRequestRepo
            ) {
        $this->status = $status;
        $this->visitorJobRequestRepo = $visitorJobRequestRepo;
    }    
    
    
     private function isAdministrator() {
        return ($this->status->getUserRole() == RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isVisitor() {
        return ($this->status->getUserRole() == RoleEnum::VISITOR);
    }
    
    private function isCurrentVisitor() {
        $requestedLogName = $this->getSingleRouteSegment()->getChildId();
        
        return ($this->status->getUserRole() == RoleEnum::VISITOR and 
                $this->status->getUserLoginName() == $requestedLogName )
                ;
    }
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof VisitorJobRequestInterface) {
            $this->visitorJobRequest = $entity;
        } else {
            $cls = VisitorJobRequestInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isItemEditable(): bool {
        $this->loadVisitorJobRequest();
        return $this->isVisitor() || $this->isAdministrator();
    }
    
    private function loadVisitorJobRequest() {
        if (!isset($this->visitorJobRequest)) {
            if ($this->getSingleRouteSegment()->hasChildId()) {
                $this->visitorJobRequest = $this->visitorJobRequestRepo->get($this->getSingleRouteSegment()->getChildId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }    
    
    public function getIterator() {         
                                  
        $this->loadVisitorJobRequest();
        $visitorEmail = $this->status->getUserEmail();

        if ($this->getSingleRouteSegment()->hasChildId()) {
            $item = [
                //route
                'actionSave' => $this->getSingleRouteSegment()->getSavePath(),
                'actionRemove' => $this->getSingleRouteSegment()->getRemovePath(),
                'id' => $this->getSingleRouteSegment()->getChildId(),
                // data
                'fields' => [
                        'cvEducationText' =>  $this->visitorJobRequest->getCvEducationText(),
                        'cvSkillsText' =>     $this->visitorJobRequest->getCvSkillsText(),
                        'name' =>     $this->visitorJobRequest->getName(),
                        'phone' =>    $this->visitorJobRequest->getPhone(),
                        'postfix' =>  $this->visitorJobRequest->getPostfix(),
                        'prefix' =>   $this->visitorJobRequest->getPrefix(),
                        'surname' =>  $this->visitorJobRequest->getSurname(),
                        'visitorEmail' => $visitorEmail,
                    ],
                ];
        } elseif ($this->isItemEditable()) {
            $item = [
                //route
                'actionAdd' => $this->getSingleRouteSegment()->getAddPath(),
                // text
                'addHeadline' => 'Nový zájem o pozici',                
                // data
                'fields' => [],
                ];
        }        
        
        $this->appendData($item ?? []);
        return parent::getIterator();  
    }
}    
