<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobRepoInterface;

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

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            JobRepoInterface $jobRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->jobRepo = $jobRepo;
    }
    
    
    public function getIterator() {                        
        // $editable = true;                            
        $requestedId = $this->getIdentity(); // id jobu
         /** @var JobInterface $jobEntity */ 
        $jobEntity = $this->jobRepo->get($requestedId);   
        $jobCompanyId = $jobEntity->getCompanyId();        
        /** @var CompanyInterface $company */
        $company = $this->companyRepo->get($jobCompanyId);
        
        
        
        $representativeFromStatus = $this->status->getRepresentativeActions()->getRepresentative();
        
        if (isset($representativeFromStatus)) {
        
            $editable = isset($representativeFromStatus) ? ($representativeFromStatus->getCompanyId()==$requestedId) : false;                            

//                /** @var CompanyInterface $company */ 
//            $company = $this->companyRepo->get($requestedId);   

//--------------------------------------------------

            $selectEducations = [];
            $selectEducations [''] =  "vyber - povinné pole" ;
            $vzdelaniEntities = $pozadovaneVzdelaniRepo->findAll();
            /** @var PozadovaneVzdelaniInterface $vzdelaniEntity */ 
            foreach ( $vzdelaniEntities as $vzdelaniEntity ) {
                $selectEducations [$vzdelaniEntity->getStupen()] =  $vzdelaniEntity->getVzdelani() ;
            }  
        
        /** @var CompanyInterface $company */ 
        $company = $companyRepo->get($idCompany);     
            
        $companyJobEntities = $jobRepo->find( " company_id = :idCompany ",  ['idCompany'=> $idCompany ] );
        $companyJobs=[];
                 /** @var JobInterface $jEntity */ 
        foreach ($companyJobEntities as $jEntity) {               
            /** @var PozadovaneVzdelaniInterface $vzdelaniEntity */
            $vzdelaniEntity = $pozadovaneVzdelaniRepo->get( $jEntity->getPozadovaneVzdelaniStupen() );
            $vzdelani = $vzdelaniEntity->getVzdelani();
                                                                        
            $companyJobs[] = [
                'jobId' => $jEntity->getId(),
                'companyId' => $jEntity->getCompanyId(),                
                'pozadovaneVzdelaniStupen' =>  $jEntity->getPozadovaneVzdelaniStupen(),
                'nazev' =>  $jEntity->getNazev(),                
                'mistoVykonu' =>  $jEntity->getMistoVykonu(),
                'popisPozice' =>  $jEntity->getPopisPozice(),
                'pozadujeme' =>  $jEntity->getPozadujeme(),
                'nabizime' =>  $jEntity->getNabizime(),                    
                'selectEducations' =>  $selectEducations 
                ];
        }   
            
        

//------------------------------------------------------            
            
            
            
            
            
            
            
            
            
//            
//                /** @var CompanyAddressInterface $companyAddressEntity */
//            $companyAddressEntity = $this->companyAddressRepo->get($requestedId);
//            if (isset($companyAddressEntity)) {
//                    $companyAddress = [
//                        'editable' => $editable,  
//
//                        'companyId'=> $companyAddressEntity->getCompanyId(),
//                        'name'   => $companyAddressEntity->getName(),
//                        'lokace' => $companyAddressEntity->getLokace(),
//                        'psc'    => $companyAddressEntity->getPsc(),
//                        'obec'   => $companyAddressEntity->getObec()
//                        ];
//            }else {
//                    $companyAddress = [
//                        'editable' => $editable,                    
//
//                        'companyId_proInsert'=> $company->getId(),
//                        ];
//                }                   
        }
        
        $array = [
            'companyAddress' => $companyAddress,
            'name' => $company->getName()
        ];
        
        
        
        
        
        
        
        return new ArrayIterator($array);        
    }
    
    
    
    
    
}    
