<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelSingleItemAbstract;
use Component\ViewModel\ViewModelItemInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\DocumentRepoInterface;
use Events\Model\Entity\DocumentInterface;
use Model\Entity\EntityInterface;
use Events\Model\Entity\VisitorProfileInterface;
use Events\Model\Repository\VisitorProfileRepoInterface;
use Events\Model\Repository\VisitorProfileRepo;

use Events\Middleware\Events\Controler\VisitorProfileControler;

use Site\ConfigurationCache;



use Access\Enum\RoleEnum;

use ArrayIterator;
use Iterator;
use UnexpectedValueException;
use Exception;
/**
 * 
 */
class DocumentSingleViewModel extends ViewModelSingleItemAbstract implements ViewModelItemInterface {

    private $status;
    private $documentRepo;
    private $visitorProfileRepo;
    
    /**
     * 
     * @var DocumentInterface
     */
    private $document;

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
    
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof DocumentInterface) {
            $this->document = $entity;
        } else {
            $cls = DocumentInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }

    public function isDataEditable(): bool {
        $this->loadDocument();
        return $this->isAdministratorEditor();
    }
    
    private function loadDocument() {
        if (!isset($this->document)) {
            if ($this->getSingleRouteSegment()->getChildId()) {
                $this->document = $this->documentRepo->get($this->getSingleRouteSegment()->getChildId());      
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator(): Iterator {                        
        //$requestedId = $this->getItemId();  // id documentu    // -----   z routy, netreba
        $parentId =  '';  // -----  !!! z routy  // POUZIT        
        $parentId =  $this->status->getUserLoginName();  //prihlasen (loginame) tj. id v nadrizene tabulce  visitorProfile  //NEPOUZIT
        //$parentId = 'visitor';
             // -----   z routy       
        
        $requestedParTab = 'visitorprofile';
        $requestedParTabRepo =  $this->visitorProfileRepo;
        $requestedTypeDoc = VisitorProfileControler::TYPE_LETTER;
        //$requestedTypeDoc = VisitorProfileControler::TYPE_CV;

        //------------------------------------------------------
               // unikátní jména souborů pro upload
        $userHash = $this->status->getUserLoginHash();
        $accept = implode(", ", ConfigurationCache::eventsUploads()['upload.events.acceptedextensions']);
        $uploadedFilename = VisitorProfileControler::UPLOADED_KEY.$userHash;
        //-------------------------------------------------------------------------------------
        
//        $editableItem = $this->isAdministratorEditor() || $this->isCurrentVisitor($parentId);           
//        
//        if (isset($parentId)) {
//            $componentRouteSegment = "events/v1/$requestedParTab/$parentId/doctype/$requestedTypeDoc";   
//            $visitorProfile = $requestedParTabRepo->get($parentId);
//            
//            if ($requestedTypeDoc == VisitorProfileControler::TYPE_LETTER) {
//                $addHeadline = "Soubor typu:  motivační dopis ";
//                $id = $visitorProfile->getLetterDocument();   
//            }
//            if ($requestedTypeDoc == VisitorProfileControler::TYPE_CV) {
//                $addHeadline = "Soubor typu: životopis ";
//                $id = $visitorProfile->getCvDocument();
//            }
//
//            if (isset($id)) {                         
//                /** @var DocumentInterface $document */
//                $document = $this->documentRepo->get($id);     
//            }
//            if (isset($document)) {
//                $documentArr = [
//                    // conditions
//                    'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
//                    //route
//                'actionSave' => $componentRouteSegment."/$id",
//                'actionRemove' => $componentRouteSegment."/$id/remove",
//
//                    'addHeadline' => $addHeadline,
//                    'uploadedFilename' => $uploadedFilename,
//                    'filename'    => $document->getDocumentFilename(),
//                    'visitorDocumentId'   => $document->getId(),
//                    'accept' => $accept,                
//                ];           
//            } elseif (  $requestedParTabRepo->get($parentId)) {  // validace id rodiče
//                    $documentArr = [
//                    // conditions
//                        'editable' => true,
//                        // text
//                        'addHeadline' => 'Přidej dokument',                      
//                        //route
//                'actionAdd' => $componentRouteSegment,
//                        // data
//                        'addHeadline' => $addHeadline,                    
//                        'uploadedFilename' => $uploadedFilename,
//                        'accept' => $accept,                    
//                        ];
//                } else {
//                    throw new UnexpectedValueException("Neexistuje profil návštěvníka s požadovaným id.");
//                }
//
//        } else {
//            $addHeadline = "Neexistuje profil návštěvníka (s požadovaným id).";
//            
//            $documentArr = [
//                'editable' => false,
//                'addHeadline' => $addHeadline, 
//                'accept' => $accept, 
//            ];
//                //throw new UnexpectedValueException("Neexistuje profil návštěvníka s požadovaným id.");
//        }  
//        
//// $documentArr = [];
//       
//        
//        $this->appendData($documentArr);
//        return parent::getIterator();    
###############        
        $this->loadDocument();
        $componentRouteSegment = 'events/v1/document';   //TODO: getRouteSegment() do abstractu - obdobně jako ve ViewModelFamilyAbstract

        $id = $this->document->getId();
        if (isset($id)) {
            $item = [
                //route
                'actionSave' => $componentRouteSegment."/$id",
//                'actionRemove' => $componentRouteSegment."/$id/remove",  !! příliš nebezpečná operace
                // data
                'fields' => [
                    'uploadedFilename' => $uploadedFilename,
                    'filename'    => $this->document->getDocumentFilename(),
                    'accept' => $accept, ],
                ];
        } elseif ($this->isDataEditable()) {
            $item = [
                //route
                'actionAdd' => $componentRouteSegment,
                // text
                'addHeadline' => 'Přidej dokument',                
                // data
                'fields' => [
                    'uploadedFilename' => $uploadedFilename,
                    'accept' => $accept, ],
                ];
        } else {
            $item = [];
        }
        
        $this->appendData($item);
        return parent::getIterator();        
        
    }
    
    
    
    
    
}    
