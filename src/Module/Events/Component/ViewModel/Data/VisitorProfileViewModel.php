<?php
namespace Events\Component\ViewModel\Data;

use Site\ConfigurationCache;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Auth\Model\Entity\LoginAggregateFullInterface;
use Status\Model\Entity\StatusSecurityInterface;

use Events\Model\Repository\VisitorProfileRepoInterface;
use Events\Model\Repository\DocumentRepoInterface;

use Events\Model\Entity\VisitorProfileInterface;
use Events\Model\Entity\DocumentInterface;

use Component\ViewModel\ViewModelInterface;
use Events\Middleware\Events\Controler\VisitorProfileControler;


use ArrayIterator;

/**
 * 
 */
class VisitorProfileViewModel extends ViewModelAbstract implements ViewModelInterface {

    private $status;
    private $secutityRepo;
    private $visitorProfileRepo;
    private $documentRepo;


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
    
    
    public function getIterator() {     

        /** @var StatusSecurityInterface $statusSecurity */
        $statusSecurity = $this->secutityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregate */
        $loginAggregate = $statusSecurity->getLoginAggregate();
        
        $editable = false;
        $documents = [];
        $profileData = [];
        $profileData = ['documents' => $documents];
        
        $requestedLogName = $this->getRequestedId();

        if (isset( $loginAggregate))  {            

            $statusLoginName = $loginAggregate->getLoginName();

            if ( $requestedLogName ==  $statusLoginName) {    //jen tetnto uzivatel
                $editable = true;        

                $userHash = $loginAggregate->getLoginNameHash();
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
                        $profileData  = [
                            'editable' => $editable,  
                   //         'visitorProfile' => $visitorProfile,

                            'cvEducationText' =>  $visitorProfile->getCvEducationText(),
                            'cvSkillsText' =>     $visitorProfile->getCvSkillsText(),
                            'name' =>     $visitorProfile->getName(),
                            'phone' =>    $visitorProfile->getPhone(),
                            'postfix' =>  $visitorProfile->getPostfix(),
                            'prefix' =>   $visitorProfile->getPrefix(),
                            'surname' =>  $visitorProfile->getSurname(),

                            'visitorEmail' => $visitorEmail,
                            'documents' => $documents                   
                            ];
                }else {
                        $profileData = [
                            'editable' => $editable,                    

                            'loginName_proInsert'=> $loginName,
                            ];
                }          
            
        }
        }
    
                                  
        $array = [
            'editable' => $editable,    
            
            'profileData' => $profileData,
            
        ];           
        
        
   
    
        return new ArrayIterator($array);        
    }
    
    
    
    
    
}    
