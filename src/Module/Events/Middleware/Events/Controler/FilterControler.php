<?php
namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;

use FrontControler\FrontControlerAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

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
    const FILTER_TAGS = 'filter_tags';
    const FILTER_COMPANY = 'filter_company';


    public function filterJob(ServerRequestInterface $request) {
                
        $tags = (new RequestParams())->getParsedBodyParam($request, self::FILTER_TAGS );  // když není žádný checkbox zaškrtnut => nejsou POST data => $data=null
        $selectCompanyId = (new RequestParams())->getParsedBodyParam($request, self::FILTER_COMPANY );   //AuthControler::NULL_VALUE;   
        
        $statusPresentation = $this->statusPresentationRepo->get();
        if (isset($statusPresentation) ) {
            $statusPresentation->setInfo(self::FILTER, [self::FILTER_TAGS=>$tags, self::FILTER_COMPANY=>$selectCompanyId]);  
        } else {
            throw new LogicException("Neexistuje status presentation.");
        }
        
        return $this->redirectSeeLastGet($request);         
    }
    
    
    
    
     public function cleanFilterJob(ServerRequestInterface $request) {                
        
        $statusPresentation = $this->statusPresentationRepo->get();
        if (isset($statusPresentation) ) {
            $statusPresentation->setInfo(self::FILTER, [self::FILTER_TAGS=> null, self::FILTER_COMPANY=> '' ]);  
        } else {
            throw new LogicException("Neexistuje status presentation.");
        }       
        
        return $this->redirectSeeLastGet($request);         
    }
        
//            $this->addFlashMessage(" ");               
          
}

