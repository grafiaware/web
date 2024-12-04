<?php
namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;

use FrontControler\FrontControlerAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Events\Model\Repository\VisitorProfileRepo;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Repository\DocumentRepo;

//TODO: chybný namespace Red
use Red\Model\Entity\LoginAggregateFullInterface;

use Events\Model\Entity\VisitorProfile;
use Events\Model\Entity\Document;
use Events\Model\Entity\DocumentInterface;
use Events\Model\Entity\VisitorJobRequest;
use Events\Model\Entity\VisitorJobRequestInterface;

use Status\Model\Enum\FlashSeverityEnum;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

use Pes\Http\Helper\RequestStatus;
use Pes\Http\Request\RequestParams;

use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response;

use Mail\Mail;
use Mail\MessageFactory\HtmlMessage;
use Mail\Params;
use Mail\Params\Content;
use Mail\Params\Attachment; 
use Mail\Params\StringAttachment;
use Mail\Params\Party;


/**
 * Description of DocumentControler
 *
 * @author vlse2610
 */
class DocumentControler extends FrontControlerAbstract {

    const UPLOADED_KEY_CV = "visitor-cv";
    const UPLOADED_KEY_LETTER = "visitor-letter";

  //  private $visitorProfileRepo;
  //  private $visitorJobRequestRepo;
    
    private $documentRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,                       
            DocumentRepo $documentRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);        
        $this->documentRepo = $documentRepo;
    }

    
    
    
    
    
    
    
    public function remove(ServerRequestInterface $request, $id) {
             
            $document = $this->documentRepo->get($id);
            if (!isset($document)) {                
            }
            else{
                $this->documentRepo->remove($document);                                
            } 

            $this->addFlashMessage(" Document smazán.");
            return $this->redirectSeeLastGet($request);        
    }

    
    
          
}

