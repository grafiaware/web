<?php
namespace Events\Component\ViewModel\Data;

use Site\ConfigurationCache;

use Component\ViewModel\StatusViewModelInterface;

use Component\ViewModel\ViewModelItemAbstract;
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
class VisitorProfileViewModel extends  ViewModelItemAbstract implements ViewModelItemInterface {

    private $status;
    private $secutityRepo;
    private $visitorProfileRepo;
    private $documentRepo;

    /**
     * 
     * @var VisitorProfileInterface
     */
    private $visitorProfile;

    public function __construct(
            StatusViewModelInterface $status,
            StatusSecurityRepo $secutityRepo,
            VisitorProfileRepoInterface $visitorProfileRepo,
            DocumentRepoInterface $documentRepo

            ) {
        $this->status = $status;
        $this->secutityRepo = $secutityRepo;
        $this->visitorProfileRepo = $visitorProfileRepo;
        $this->documentRepo = $documentRepo;
    }    
    
    
     private function isAdministrator() {
        return ($this->status->getUserRole() == RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isVisitor() {
        return ($this->status->getUserRole() == RoleEnum::VISITOR);
    }
    
    private function isCurrentVisitor($ItemId) {
        $userLoginName = $this->status->getUserLoginName();
        $requestedLogName = $this->getItemId();
        return ($this->status->getUserRole() == RoleEnum::VISITOR and 
                $this->status->getUserLoginName() == $ItemId )
                ;
    }
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof VisitorProfileInterface) {
            $this->company = $entity;
        } else {
            $cls = VisitorProfileInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isItemEditable(): bool {
        $this->loadVisitorProfile();
        return $this->isVisitor();
    }
    
    private function loadVisitorProfile() {
        if (!isset($this->visitorProfile)) {
            if ($this->hasItemId()) {
                $this->visitorProfile = $this->visitorProfileRepo->get($this->getItemId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }    
    
    public function getIterator() {     
        $this->loadVisitorProfile();
    
        $requestedLogName = $this->getItemId();
        $isAdministrator = $this->isAdministrator();
        $editable =  $this->isAdministrator() || $this->isCurrentVisitor($this->getItemId());
        
        
        /** @var StatusSecurityInterface $statusSecurity */
        $statusSecurity = $this->secutityRepo->get();
        
              
         /*  ***********************************************************  */       
        /** @var LoginAggregateFullInterface $loginAggregate */
        $loginAggregate = $statusSecurity->getLoginAggregate();
//        $statusLoginName = $loginAggregate->getLoginName(); 
        $statusLoginName =  $this->status->getUserLoginName();
       
       // $editable = false;
       // $visible = true;
        $documents = [];
        $profileData = [];
        $profileData = [ 'editable' => $editable ]  ;
        $documents = [ 'editable' => $editable ]  ;
        
       
        

//         /** @var VisitorProfileRepoInterface $visitorProfileRepo */
//        $visitorProfileRepo = $this->visitorProfileRepo;
//                /** @var VisitorProfileInterface $visitorProfile */
//        $visitorProfile = $visitorProfileRepo->get($loginName);  
        
        
        
        
        
        
//        if (isset( $loginAggregate))  {            

            //$statusLoginName = $loginAggregate->getLoginName();

        
        
        
        
        
        
        
  //          if ( $requestedLogName ==  $statusLoginName) {    //jen tento (prihlaseny) uzivatel
//                $editable = true;   
//                $visible = true;

                //$userHash = $loginAggregate->getLoginNameHash();
                $userHash =  $this->status->getUserLoginHash();
                $accept = implode(", ", ConfigurationCache::eventsUploads()['upload.events.acceptedextensions']);
                $uploadedCvFilename = VisitorProfileControler::UPLOADED_KEY_CV.$userHash;
                $uploadedLetterFilename = VisitorProfileControler::UPLOADED_KEY_LETTER.$userHash;

                $visitorEmail = $loginAggregate->getRegistration() ? $loginAggregate->getRegistration()->getEmail() : '';
            //------------------------------------------------------------------

                /** @var StatusViewModelInterface $statusViewModel */
                $role = $this->status->getUserRole();
                $loginName =  $this->status->getUserLoginName();


                /** @var VisitorProfileRepoInterface $visitorProfileRepo */
                $visitorProfileRepo = $this->visitorProfileRepo;
                /** @var VisitorProfileInterface $visitorProfile */
                $visitorProfile = $visitorProfileRepo->get($loginName);   

                if (isset($visitorProfile)) {
                    $documentCvId = $visitorProfile->getCvDocument();
                    $documentLettterId = $visitorProfile->getLetterDocument();        
                }
                /** @var DocumentInterface $visitorDocumentCv */
                if (isset($documentCvId))  {
                    $visitorDocumentCv = $this->documentRepo->get($documentCvId);        
                }

                /** @var DocumentInterface $visitorDocumentLetter */
                if (isset($documentLettterId)) {
                    $visitorDocumentLetter = $this->documentRepo->get($documentLettterId);         
                }

                $documents = [];
                $documents = [
                    'visitorDocumentCvFilename' => isset($visitorDocumentCv) ? $visitorDocumentCv->getDocumentFilename() : '',
                    'visitorDocumentLetterFilename' => isset($visitorDocumentLetter) ? $visitorDocumentLetter->getDocumentFilename() : '',
                    'visitorDocumentCvId' => isset($visitorDocumentCv) ? $visitorDocumentCv->getId() : '',
                    'visitorDocumentLetterId' => isset($visitorDocumentLetter) ? $visitorDocumentLetter->getId() : '',

                    'editable' => $editable, 
                    'uploadedCvFilename' => ($uploadedCvFilename) ?? '',
                    'uploadedLetterFilename' => ($uploadedLetterFilename) ?? '',
                    'accept' => $accept ?? ''

                ];

                if (isset($visitorProfile)) {
                        $item /*profileData*/  = [
                            'editable' => $editable,  

                            'cvEducationText' =>  $visitorProfile->getCvEducationText(),
                            'cvSkillsText' =>     $visitorProfile->getCvSkillsText(),
                            'name' =>     $visitorProfile->getName(),
                            'phone' =>    $visitorProfile->getPhone(),
                            'postfix' =>  $visitorProfile->getPostfix(),
                            'prefix' =>   $visitorProfile->getPrefix(),
                            'surname' =>  $visitorProfile->getSurname(),
                            'visitorEmail' => $visitorEmail,
                                           
                            ];
                }else {
                        $item = [
                            'editable' => $editable,                    

                            'loginName_proInsert'=> $loginName,
                            ];
                }          
            
//            } else {
//                $visible = false;
//            }
//        }
    
                                  
        $array = [
            'editable' => $editable,    
            'visible' => $visible,            
            
            'profileData' => $profileData,
            'documents' => $documents    
            
        ];           
        
        
   
        $this->appendData($array);
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
        
        $editableItem = $this->isAdministratorEditor() || $this->isCurrentVisitor($parentId);           
        
        if (isset($parentId)) {
            $componentRouteSegment = "events/v1/$requestedParTab/$parentId/doctype/$requestedTypeDoc";   
            $visitorProfile = $requestedParTabRepo->get($parentId);
            
            if ($requestedTypeDoc == VisitorProfileControler::TYPE_LETTER) {
                $addHeadline = "Soubor typu:  motivační dopis ";
                $id = $visitorProfile->getLetterDocument();   
            }
            if ($requestedTypeDoc == VisitorProfileControler::TYPE_CV) {
                $addHeadline = "Soubor typu: životopis ";
                $id = $visitorProfile->getCvDocument();
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
