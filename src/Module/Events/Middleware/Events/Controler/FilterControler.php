<?php
namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;

use FrontControler\FrontControlerAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Events\Model\Repository\JobToTagRepo;
use Events\Model\Repository\JobTagRepo;



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




/**
 * Description of FilterControler
 *
 * @author vlse2610
 */
class FilterControler extends FrontControlerAbstract {

    const UPLOADED_KEY_CV = "visitor-cv";
    const UPLOADED_KEY_LETTER = "visitor-letter";
  
    
    private $jobToTagRepo;
    private $jobTagRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            
            JobToTagRepo $jobToTagRepo,
            JobTagRepo $jobTagRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);        
        $this->jobToTagRepo = $jobToTagRepo;
        $this->jobTagRepo = $jobTagRepo;
    }

    
    
    public function filterJob(ServerRequestInterface $request) {
        
        
        $data = (new RequestParams())->getParsedBodyParam($request, "data" );  // když není žádný checkbox zaškrtnut => nejsou POST data => $data=null

        
        
        return $this->redirectSeeLastGet($request);         
    }
    
    
    
   
//    public function remove(ServerRequestInterface $request, $id) {
//             
//            $document = $this->documentRepo->get($id);
//            if (!isset($document)) {                
//            }
//            else{
//                $this->documentRepo->remove($document);                                
//            } 
//
//            $this->addFlashMessage(" Document smazán.");
//            return $this->redirectSeeLastGet($request);        
//    }

    
    
          
}

