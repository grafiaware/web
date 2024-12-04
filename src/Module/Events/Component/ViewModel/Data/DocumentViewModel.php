<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelItemAbstract;
use Component\ViewModel\ViewModelItemInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\DocumentRepoInterface;
use Events\Model\Entity\DocumentInterface;
use Events\Model\Entity\VisitorProfileInterface;
use Events\Model\Repository\VisitorProfileRepoInterface;
use Events\Model\Repository\VisitorProfileRepo;



use Access\Enum\RoleEnum;

use ArrayIterator;
use UnexpectedValueException;
/**
 * 
 */
class DocumentViewModel extends ViewModelItemAbstract implements ViewModelItemInterface {

    private $status;
    private $documentRepo;
    private $visitorProfileRepo;

    use RepresentativeTrait;

    public function __construct(
            StatusViewModelInterface $status,
            DocumentRepoInterface $documentRepo,
            VisitorProfileRepoInterface $visitorProfileRepo
            ) {
        $this->status = $status;
        $this->documentRepo = $documentRepo;
        $this->visitorProfileRepo = $visitorProfileRepo;        
    }
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

//    private function isCompanyEditor($companyId) {
//        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
//    }   
    
    private function isCurrentVisitor($requestedId) {
        //zda je visitor a
        //zda je to visitor, jehoz je to profile
        //!!!!!!!!!!!!!
        $is = $this->status->getUserLoginName() == $requestedId;
        
        return ( ($this->status->getUserRole()== RoleEnum::VISITOR)  AND $is ) ;
    }
    
    public function getIterator() {                        
        $requestedId = $this->getItemId();  // id documentu
        $requestedTabId = 'visitor';  // (loginame) tj. id v nadrizene tabulce
        $requestedTab = 'visitorprofile';
        $requestedTypeDoc = 'letter';
        
        
        $isAdministrator = $this->isAdministrator();
        
        $editableItem = $isAdministrator || $this->isCurrentVisitor($requestedTabId);    
        $componentRouteSegment = "events/v1/$requestedTab/doctype/$requestedTypeDoc/id/$requestedId";
        
       
        $visitorProfile = $this->visitorProfileRepo->get($requestedTabId);
        $addHeadline = "";
                
        /** @var DocumentInterface $document */
        $document = $this->documentRepo->get($requestedId);     
        if (isset($document)) {
            $documentArr = [
                // conditions
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                'remove'=> $isAdministrator,   // přidá tlačítko remove do item
                //route
                'componentRouteSegment' => $componentRouteSegment,
                'id' => $requestedTabId,
                // data
                'addHeadline' => $addHeadline,
            
                'fields' => [
                    'editable' => $editableItem,
                    'name'   => $document->getName(),
                    'lokace' => $document->getLokace(),
                    'psc'    => $document->getPsc(),
                    'obec'   => $document->getObec()
                ],                
            ];           
        } elseif ($editableItem) {
            /** @var CompanyInterface $company */ 
            if ($this->visitorProfileRepo->get($requestedTabId)) {  // validace id rodiče
                $documentArr = [
                // conditions
                    'editable' => true,
                    'add' => true,   // zobrazí se tlačítko Uložit   ????
                    // text
                    'addHeadline' => 'Přidej dokument',                      
                    'companyId'=> $requestedTabId,
                    //route
                    'componentRouteSegment' => $componentRouteSegment,
                    // data
                    'addHeadline' => $addHeadline,

                    'fields' => [
                        'editable' => $editableItem,]                    
                    ];
            } else {
                throw new UnexpectedValueException("Neexistuje profil návštěvníka s požadovaným id.");
            }
        } else {
            $documentArr = [];
        }
        $this->appendData($documentArr);
        return parent::getIterator();        
    }
    
    
    
    
    
}    
