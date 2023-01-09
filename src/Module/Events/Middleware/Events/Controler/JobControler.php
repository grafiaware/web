<?php 

namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;
use FrontControler\FrontControlerAbstract;
use Auth\Model\Entity\LoginAggregateFullInterface;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Events\Model\Repository\RepresentativeRepoInterface;
use Events\Model\Repository\RepresentativeRepo;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\DocumentRepoInterface;

use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Entity\Representative;
use Events\Model\Entity\Company;
use Events\Model\Entity\Job;
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
 * 
 */
class JobControler extends FrontControlerAbstract {        
    /**
     * 
     * @var CompanyRepoInterface
     */
    private $companyRepo;
    /**
     * 
     * @var RepresentativeRepoInterface
     */
    private $representativeRepo;    
    /**
     * 
     * @var JobRepoInterface
     */
    private $jobRepo;
     /**
     * 
     * @var VisitorJobRequestRepoInterface
     */
    private $visitorJobRequestRepo;

    /**
     * @var DocumentRepoInterface
     */
    private $documentRepo;
    
       
    /**
     * 
     * @param StatusSecurityRepo $statusSecurityRepo
     * @param StatusFlashRepo $statusFlashRepo
     * @param StatusPresentationRepo $statusPresentationRepo
     * @param CompanyRepoInterface $companyRepo
     * @param RepresentativeRepoInterface $representativeRepo
     * @param VisitorJobRequestRepo $visitorJobRequestRepo
     * @param JobRepoInterface $jobRepo
     * @param DocumentRepoInterface $documentRepo
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            
            CompanyRepoInterface $companyRepo,           
            RepresentativeRepoInterface $representativeRepo,
            VisitorJobRequestRepo $visitorJobRequestRepo,            
            JobRepoInterface $jobRepo,
            DocumentRepoInterface $documentRepo
            
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->companyRepo = $companyRepo;     
        $this->representativeRepo = $representativeRepo;
        $this->visitorJobRequestRepo = $visitorJobRequestRepo;
        $this->jobRepo = $jobRepo;
        $this->documentRepo = $documentRepo;
    }
    
   
    
    
 }   