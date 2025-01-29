<?php
namespace Events\Component\ViewModel\Data;

use Site\ConfigurationCache;

use Component\ViewModel\StatusViewModelInterface;

use Component\ViewModel\ViewModelSingleItemAbstract;
use Component\ViewModel\ViewModelItemInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Auth\Model\Entity\LoginAggregateFullInterface;
use Status\Model\Entity\StatusSecurityInterface;

use Events\Model\Repository\VisitorProfileRepoInterface;
use Events\Model\Repository\DocumentRepoInterface;

use Events\Model\Entity\VisitorProfileInterface;
use Events\Model\Entity\DocumentInterface;

use Events\Middleware\Events\Controler\VisitorProfileControler;
use Model\Entity\EntityInterface;

use Access\Enum\RoleEnum;
use ArrayIterator;

/**
 * 
 */
class VisitorProfileSingleItemViewModel extends  ViewModelSingleItemAbstract implements ViewModelItemInterface {

    private $status;
    private $visitorProfileRepo;

    /**
     * 
     * @var VisitorProfileInterface
     */
    private $visitorProfile;

    public function __construct(
            StatusViewModelInterface $status,
            VisitorProfileRepoInterface $visitorProfileRepo
            ) {
        $this->status = $status;
        $this->visitorProfileRepo = $visitorProfileRepo;
    }    
    
    
     private function isAdministrator() {
        return ($this->status->getUserRole() == RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isVisitor() {
        return ($this->status->getUserRole() == RoleEnum::VISITOR);
    }
    
    private function isProfileCreator() {
        $requestedLogName = $this->getSingleRouteSegment()->getChildId();
        return ($this->isVisitor() && $this->status->getUserLoginName() == $requestedLogName );
    }
    
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof VisitorProfileInterface) {
            $this->visitorProfile = $entity;
        } else {
            $cls = VisitorProfileInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isItemEditable(): bool {
        $this->loadVisitorProfile();
        return $this->isVisitor() || $this->isAdministrator();
    }
    
    private function loadVisitorProfile() {
        if (!isset($this->visitorProfile)) {
            if ($this->getSingleRouteSegment()->hasChildId()) {
                $childId = $this->getSingleRouteSegment()->getChildId();
                $this->visitorProfile = $this->visitorProfileRepo->get($childId);     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }    
    
    public function getIterator() {     
        
//        $isAdministrator = $this->isAdministrator();
//        $editable =  $this->isAdministrator() || $this->isCurrentVisitor();
//                //$userHash = $loginAggregate->getLoginNameHash();
//                $userHash =  $this->status->getUserLoginHash();
//                $accept = implode(", ", ConfigurationCache::eventsUploads()['upload.events.acceptedextensions']);
//                $uploadedCvFilename = VisitorProfileControler::UPLOADED_KEY_CV.$userHash;
//                $uploadedLetterFilename = VisitorProfileControler::UPLOADED_KEY_LETTER.$userHash;
//
//            //------------------------------------------------------------------
//
//                /** @var StatusViewModelInterface $statusViewModel */
//                $role = $this->status->getUserRole();
// 

//                if (isset($this->visitorProfile)) {
//                    $documentCvId = $this->visitorProfile->getCvDocument();
//                    $documentLettterId = $this->visitorProfile->getLetterDocument();        
//                }
//                /** @var DocumentInterface $visitorDocumentCv */
//                if (isset($documentCvId))  {
//                    $visitorDocumentCv = $this->documentRepo->get($documentCvId);        
//                }
//
//                /** @var DocumentInterface $visitorDocumentLetter */
//                if (isset($documentLettterId)) {
//                    $visitorDocumentLetter = $this->documentRepo->get($documentLettterId);         
//                }
//                $documents = [
//                    'visitorDocumentCvFilename' => isset($visitorDocumentCv) ? $visitorDocumentCv->getDocumentFilename() : '',
//                    'visitorDocumentLetterFilename' => isset($visitorDocumentLetter) ? $visitorDocumentLetter->getDocumentFilename() : '',
//                    'visitorDocumentCvId' => isset($visitorDocumentCv) ? $visitorDocumentCv->getId() : '',
//                    'visitorDocumentLetterId' => isset($visitorDocumentLetter) ? $visitorDocumentLetter->getId() : '',
//
//                    'editable' => $editable, 
//                    'uploadedCvFilename' => ($uploadedCvFilename) ?? '',
//                    'uploadedLetterFilename' => ($uploadedLetterFilename) ?? '',
//                    'accept' => $accept ?? ''
//
//                ];
//                $nn = 'Balíček pracovních údajů';
//
//                if (isset($this->visitorProfile)) {
//                    $item = [
//                        'cvEducationText' =>  $this->visitorProfile->getCvEducationText(),
//                        'cvSkillsText' =>     $this->visitorProfile->getCvSkillsText(),
//                        'name' =>     $this->visitorProfile->getName(),
//                        'phone' =>    $this->visitorProfile->getPhone(),
//                        'postfix' =>  $this->visitorProfile->getPostfix(),
//                        'prefix' =>   $this->visitorProfile->getPrefix(),
//                        'surname' =>  $this->visitorProfile->getSurname(),
//                        'visitorEmail' => $visitorEmail,
//
//                        ];
//                } else {
//                    $item = [
//                        'loginName_proInsert'=> $loginName,
//                        ];
//                }             
                                  
        $this->loadVisitorProfile();
        $visitorEmail = $this->status->getUserEmail();

        if ($this->getSingleRouteSegment()->hasChildId() && isset($this->visitorProfile)) {
            $item = [
                //route
                'actionSave' => $this->getSingleRouteSegment()->getSavePath(),
//                'actionRemove' => $this->getSingleRouteSegment()->getRemovePath(),
                'id' => $this->getSingleRouteSegment()->getChildId(),
                // data
                'fields' => [
                        'cvEducationText' =>  $this->visitorProfile->getCvEducationText(),
                        'cvSkillsText' =>     $this->visitorProfile->getCvSkillsText(),
                        'name' =>     $this->visitorProfile->getName(),
                        'phone' =>    $this->visitorProfile->getPhone(),
                        'postfix' =>  $this->visitorProfile->getPostfix(),
                        'prefix' =>   $this->visitorProfile->getPrefix(),
                        'surname' =>  $this->visitorProfile->getSurname(),
                        'visitorEmail' => $visitorEmail,
                    ],
                ];
        } elseif ($this->isItemEditable()) {
            $item = [
                //route
                'actionAdd' => $this->getSingleRouteSegment()->getAddPath(),
                'titleAdd' => 'Uložit údaje profilu',
                // text
                'addHeadline' => 'Nový profil návštěvníka',                
                // data
                'fields' => [
                        'visitorEmail' => $visitorEmail,                    
                ],
                ];
        }        
        
        $this->appendData($item ?? []);
        return parent::getIterator();  
    }
    
########################

    public function getIteratorZDocumentViewModel() {                        
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
        
        $editableItem = $this->isAdministratorEditor() || $this->isProfileCreator($parentId);           
        
        if (isset($parentId)) {
            $componentRouteSegment = "events/v1/$requestedParTab/$parentId/doctype/$requestedTypeDoc";   
            $this->visitorProfile = $requestedParTabRepo->get($parentId);
            
            if ($requestedTypeDoc == VisitorProfileControler::TYPE_LETTER) {
                $addHeadline = "Soubor typu:  motivační dopis ";
                $id = $this->visitorProfile->getLetterDocument();   
            }
            if ($requestedTypeDoc == VisitorProfileControler::TYPE_CV) {
                $addHeadline = "Soubor typu: životopis ";
                $id = $this->visitorProfile->getCvDocument();
            }

            if (isset($id)) {                         
                /** @var DocumentInterface $document */
                $document = $this->documentRepo->get($id);     
            }
            if (isset($document)) {
                $documentArr = [
                    // conditions
                    'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                    //route
                'actionSave' => $componentRouteSegment."/$id",
                'actionRemove' => $componentRouteSegment."/$id/remove",

                    'addHeadline' => $addHeadline,
                    'uploadedFilename' => $uploadedFilename,
                    'filename'    => $document->getDocumentFilename(),
                    'visitorDocumentId'   => $document->getId(),
                    'accept' => $accept,                
                ];           
            } elseif (  $requestedParTabRepo->get($parentId)) {  // validace id rodiče
                    $documentArr = [
                    // conditions
                        'editable' => true,
                        // text
                        'addHeadline' => 'Přidej dokument',                      
                        //route
                'actionAdd' => $componentRouteSegment,
                        // data
                        'addHeadline' => $addHeadline,                    
                        'uploadedFilename' => $uploadedFilename,
                        'accept' => $accept,                    
                        ];
                } else {
                    throw new UnexpectedValueException("Neexistuje profil návštěvníka s požadovaným id.");
                }

        } else {
            $addHeadline = "Neexistuje profil návštěvníka (s požadovaným id).";
            
            $documentArr = [
                'editable' => false,
                'addHeadline' => $addHeadline, 
                'accept' => $accept, 
            ];
                //throw new UnexpectedValueException("Neexistuje profil návštěvníka s požadovaným id.");
        }  
        
// $documentArr = [];
       
        
        $this->appendData($documentArr);
        return parent::getIterator();   
    }
    
    
    
}    
