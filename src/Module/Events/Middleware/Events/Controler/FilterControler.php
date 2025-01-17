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


use Status\Model\Enum\FlashSeverityEnum;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

use Pes\Http\Helper\RequestStatus;
use Pes\Http\Request\RequestParams;

use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response;

use LogicException;


/**
 * Description of FilterControler
 *
 * @author vlse2610
 */
class FilterControler extends FrontControlerAbstract {
  
    const FILTER = 'filter';

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
                
        $tags = (new RequestParams())->getParsedBodyParam($request, "filterDataTags" );  // když není žádný checkbox zaškrtnut => nejsou POST data => $data=null
        $selectCompanyId = (new RequestParams())->getParsedBodyParam($request, "filterSelectCompany" );   //AuthControler::NULL_VALUE;   
        
        $statusPresentation = $this->statusPresentationRepo->get();
        if (isset($statusPresentation) ) {
            $statusPresentation->setInfo(self::FILTER, ['filterDataTags'=>$tags, 'companyId'=>$selectCompanyId]);  
            //$this->statusPresentationRepo->get()->setInfo(self::FILTER, ['data'=>$data, 'selectCompany'=>$selectCompany]);            
        } else {
            throw new LogicException("Neexistuje status presentation.");
        }
        
        return $this->redirectSeeLastGet($request);         
    }
    
    
    
    
     public function cleanFilterJob(ServerRequestInterface $request) {
                
        $data = (new RequestParams())->getParsedBodyParam($request, "data" );  // když není žádný checkbox zaškrtnut => nejsou POST data => $data=null                
        $selectCompany = (new RequestParams())->getParsedBodyParam($request, "selectCompany" ); 
        
        $data = [];
        $selectCompany = "";
        
        return $this->redirectSeeLastGet($request);         
    }
    
    

//            $this->addFlashMessage(" Document smazán.");           
    
          
}

