<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\PozadovaneVzdelaniRepoInterface;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\JobInterface;

use Component\ViewModel\ViewModelInterface;


use ArrayIterator;

/**
 * 
 */
class CompanyJobsListViewModel extends ViewModelAbstract implements ViewModelInterface {

    private $status;       
    private $companyRepo;
    private $jobRepo;
    private $pozadovaneVzdelaniRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            JobRepoInterface $jobRepo,
            PozadovaneVzdelaniRepoInterface $pozadovaneVzdelaniRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->jobRepo = $jobRepo;
        $this->pozadovaneVzdelaniRepo = $pozadovaneVzdelaniRepo;    
    }    
    
    
    public function getIterator() {                        
        $requestedId = $this->getIdentity(); // id company
         /** @var CompanyInterface $company */ 
        $company = $this->companyRepo->get($requestedId);   
        $companyJobs=[];
        
            $selectEducations = [];
            $selectEducations [''] =  "vyber - povinné pole" ;
            $vzdelaniEntities = $this->pozadovaneVzdelaniRepo->findAll();
            /** @var PozadovaneVzdelaniInterface $vzdelaniEntity */ 
            foreach ( $vzdelaniEntities as $vzdelaniEntity ) {
                $selectEducations [$vzdelaniEntity->getStupen()] =  $vzdelaniEntity->getVzdelani() ;
            }   
        
        $representativeFromStatus = $this->status->getRepresentativeActions()->getRepresentative();    
        if (isset($representativeFromStatus)) {  
            
           $representativeCompanyId = $representativeFromStatus->getCompanyId();                 
           $editable = isset($representativeFromStatus) ? ($representativeCompanyId == $requestedId) : false;                            
  
           $companyJobEntities = $this->jobRepo->find( " company_id = :idCompany ",  ['idCompany'=> $requestedId ] );              
            //$companyJobs=[];        
         
            if (isset($companyJobEntities)) {              
                 /** @var JobInterface $jEntity */
                foreach ($companyJobEntities as $jEntity) {               
                    /** @var PozadovaneVzdelaniInterface $vzdelaniEntity */
//                    $vzdelaniEntity = $this->pozadovaneVzdelaniRepo->get( $jEntity->getPozadovaneVzdelaniStupen() );
//                    $vzdelani = $vzdelaniEntity->getVzdelani();
                                     
                    $companyJobs[] = [
                        'jobId' => $jEntity->getId(),
                        'companyId' => $jEntity->getCompanyId(),                
                        'pozadovaneVzdelaniStupen' =>  $jEntity->getPozadovaneVzdelaniStupen(),
                        'nazev' =>  $jEntity->getNazev(),                
                        'mistoVykonu' =>  $jEntity->getMistoVykonu(),
                        'popisPozice' =>  $jEntity->getPopisPozice(),
                        'pozadujeme' =>  $jEntity->getPozadujeme(),
                        'nabizime' =>  $jEntity->getNabizime(),                    
                        'selectEducations' =>  $selectEducations,

                        'editable' => $editable
                    ];                
                }      
            }   
            else {
                    $companyJobs[] = [ 
                        'editable' => $editable,  
                        'selectEducations' =>  $selectEducations,
                        ];          
            }
        }                
        
      
        $array = [
            'jobs' => $companyJobs,
            'companyName' => isset($company) ? $company->getName() : ""  ,
            'companyId' => $requestedId,
            'selectEducations' => $selectEducations,
            'editable' => $editable
                ];               
        return new ArrayIterator($array);    
  
        
    }
 
}    
