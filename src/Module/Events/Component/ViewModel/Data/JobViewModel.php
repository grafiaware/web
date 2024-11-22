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
class JobViewModel extends ViewModelAbstract implements ViewModelInterface {

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
        $requestedId = $this->getRequestedId(); // id jobu
         /** @var JobInterface $jobEntity */ 
        $jobEntity = $this->jobRepo->get($requestedId);   
        
        $selectEducations = [];
        $selectEducations [''] =  "vyber - povinné pole" ;
        $vzdelaniEntities = $this->pozadovaneVzdelaniRepo->findAll();
        /** @var PozadovaneVzdelaniInterface $vzdelaniEntity */ 
        foreach ( $vzdelaniEntities as $vzdelaniEntity ) {
            $selectEducations [$vzdelaniEntity->getStupen()] =  $vzdelaniEntity->getVzdelani() ;
        }   
        
        $representativeFromStatus = $this->status->getRepresentativeActions()->getRepresentative();       
        $companyJob=[];
        if (isset($representativeFromStatus)) {
        
            if (isset($jobEntity)) {
                $jobCompanyId = $jobEntity->getCompanyId();                
                $representativeCompanyId = $representativeFromStatus->getCompanyId();                 
                $editable = isset($representativeFromStatus) ? ($representativeCompanyId == $jobCompanyId) : false;                            
                //--------------------------------------
                      /** @var CompanyInterface $company */
                $company = $this->companyRepo->get($jobCompanyId);
                $companyJob = [
                    'jobId' => $jobEntity->getId(),
                    'companyId' => $jobEntity->getCompanyId(),                
                    'pozadovaneVzdelaniStupen' =>  $jobEntity->getPozadovaneVzdelaniStupen(),
                    'nazev' =>  $jobEntity->getNazev(),                
                    'mistoVykonu' =>  $jobEntity->getMistoVykonu(),
                    'popisPozice' =>  $jobEntity->getPopisPozice(),
                    'pozadujeme' =>  $jobEntity->getPozadujeme(),
                    'nabizime' =>  $jobEntity->getNabizime(),                    
                    'selectEducations' =>  $selectEducations,

                    'editable' => $editable
                    ];                
            }            
            else {
                    $companyJob = [ 
                        'editable' => false,  
                        'selectEducations' =>  $selectEducations,
                        ];          
            }
        }   
      
        $array = [
            'job' => $companyJob,
            'companyName' => isset($company) ? $company->getName() : "" 
        ];               
        return new ArrayIterator($array);        
    }
    
    
    
    
    
}    
