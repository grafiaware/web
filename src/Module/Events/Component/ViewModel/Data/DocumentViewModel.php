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

use Events\Middleware\Events\Controler\VisitorProfileControler;

use Site\ConfigurationCache;



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
        $parrentId =  $this->status->getUserLoginName();  // (loginame) tj. id v nadrizene tabulce   visitor
                
        $requestedParTab = 'visitorprofile';
        $requestedParTabRepo =  $this->visitorProfileRepo;
        $requestedTypeDoc = 'letter';
        //------------------------------------------------------
               // unikátní jména souborů pro upload
        $userHash = $this->status->getUserLoginHash();
        $accept = implode(", ", ConfigurationCache::eventsUploads()['upload.events.acceptedextensions']);
        $uploadedFilename = VisitorProfileControler::UPLOADED_KEY_CV.$userHash;
        //-------------------------------------------------------------------------------------
        
        $isAdministrator = $this->isAdministrator();        
        $editableItem = $isAdministrator || $this->isCurrentVisitor($parrentId);    
        
        $componentRouteSegment = "events/v1/$requestedParTab/$parrentId/doctype/$requestedTypeDoc";     
       
        $visitorProfile = $requestedParTabRepo->get($parrentId) ;
        $addHeadline = "--- Soubor --- typ:  $requestedTypeDoc ";
                
        /** @var DocumentInterface $document */
        $document = $this->documentRepo->get($requestedId);     
        if (isset($document)) {
            $documentArr = [
                // conditions
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                //route
                'componentRouteSegment' => $componentRouteSegment,
                'id' => $requestedId,
            
                'uploadedFilename' => $uploadedFilename,
                'filename'    => $document->getDocumentFilename(),
                'visitorDocumentId'   => $document->getId(),
                'accept' => $accept,                
            ];           
        } elseif ($this->visitorProfileRepo->get($parrentId)) {  // validace id rodiče
                $documentArr = [
                // conditions
                    'editable' => true,
                    // text
                    'addHeadline' => 'Přidej dokument',                      
                    //route
                    'componentRouteSegment' => $componentRouteSegment,
                    // data
                    'uploadedFilename' => $uploadedFilename,
                    'accept' => $accept,                    
                    ];
            } else {
                throw new UnexpectedValueException("Neexistuje profil návštěvníka s požadovaným id.");
            }
//        } else {
//            $documentArr = [];
//        }
        
        
        $this->appendData($documentArr);
        return parent::getIterator();        
    }
    
    
    
    
    
}    
