<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Events\Middleware\Events\Controler;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use FrontControler\FrontControlerAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\CompanyInfoRepo;
use Events\Model\Repository\CompanyVersionRepo;
use Status\Model\Enum\FlashSeverityEnum;
/**
 * Description of MaintenanceControler
 *
 * @author pes2704
 */
class MaintenanceControler extends FrontControlerAbstract {
    
    private $companyRepo;
    private $companyAddressRepo;
    private $companyContactRepo;
    private $companyInfoRepo;
    private $companyVersionRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo, 
            StatusFlashRepo $statusFlashRepo, 
            StatusPresentationRepo $statusPresentationRepo,
            CompanyRepo $companyRepo,
            CompanyAddressRepo $companyAddressRepo,
            CompanyContactRepo $companyContactRepo,
            CompanyInfoRepo $companyInfoRepo,
            CompanyVersionRepo $companyVersionRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->companyRepo = $companyRepo;
        $this->companyAddressRepo = $companyAddressRepo;
        $this->companyContactRepo = $companyContactRepo;
        $this->companyInfoRepo = $companyInfoRepo;
        $this->companyVersionRepo = $companyVersionRepo;
    }
    
    public function archiveCompanies(ServerRequestInterface $request, $sourceVersion, $targetVersion): ResponseInterface {
        // !! všechny hodnoty version musí být v databázi - v tabulce company_version (jinak nastane constraint violation)
        $archiveVersion = 'archive_'.$sourceVersion;
        
        if (!$this->checkVersions($sourceVersion, $archiveVersion, $targetVersion)) {
        return $this->createJsonPostCreatedResponse(["Message" => "Missing version fk in company_version."]);
        }
        $companies = $this->companyRepo->findByVersionAsc($sourceVersion);
        foreach ($companies as $company) {
            $id = $company->getId();
            $address = $this->companyAddressRepo->get($id);
            $contacts = $this->companyContactRepo->findByCompanyId($id);
            $info = $this->companyInfoRepo->get($id);
            
            // zdrojovou verzi naklonuje a uloží jako archivní, pak jí změní verzi na cílovou 
            // - id původní company se nemění, všechny potomkovské entity zůstávají navázány (fk) na původní company
            $archiveCompany = clone $company;
            $archiveCompany->setId(null);  // clone nemaže id!!
            $archiveCompany->setVersionFk($archiveVersion);  // company_version má UNIQUE index name+version
            $this->companyRepo->add($archiveCompany);   // CompanyDao je DaoEditAutoincrementKeyInterface -> proběhne ihned INSERT do databáze a nastavení nového id do entity
            $newId = $archiveCompany->getId();
            $company->setVersionFk($targetVersion);
            
            if (isset($address)) {
                $archiveAddress = clone $address;
                $archiveAddress->setCompanyId($newId);  // připojí klon adresy k nové company
                $this->companyAddressRepo->add($archiveAddress);
            }
            foreach ($contacts as $contact) {
                $newContact = clone $contact;
                $newContact->setCompanyId($newId);
                $this->companyContactRepo->add($newContact);
            }
            if (isset($info)) {
                $newInfo = clone $info;
                $newInfo->setCompanyId($newId);
                $this->companyInfoRepo->add($newInfo);
            }
        }
        $cnt = count($companies);
        $msg ="Archived $cnt items company in version $sourceVersion and subordinate info, address and contact to archive version $archiveVersion and changed their version to $targetVersion.";
        $this->addFlashMessage("Archived $cnt items company in version $sourceVersion and subordinate info, address and contact to archive version $archiveVersion and changed their version to $targetVersion.", FlashSeverityEnum::SUCCESS);        
        return $this->createJsonPostCreatedResponse(["Message" => "Archived $cnt items company in version $sourceVersion and subordinate info, address and contact to archive version $archiveVersion and changed their version to $targetVersion."]);
    }
    
    private function checkVersions($sourceVersion, $archiveVersion, $targetVersion) {
        $ok = true;
        $source = $this->companyVersionRepo->get($sourceVersion);
        $archive = $this->companyVersionRepo->get($archiveVersion);
        $target = $this->companyVersionRepo->get($targetVersion);
        if(!isset($source)) {
            $this->addFlashMessage("The company_version table does not contain the $sourceVersion value, which is required as a foreign key.", FlashSeverityEnum::ERROR);        
            $ok = false;
        }
        if(!isset($archive)) {
            $this->addFlashMessage("The company_version table does not contain the $archiveVersion value, which is required as a foreign key.", FlashSeverityEnum::ERROR);        
            $ok = false;
        }
        if(!isset($target)) {
            $this->addFlashMessage("The company_version table does not contain the $targetVersion value, which is required as a foreign key.", FlashSeverityEnum::ERROR);        
            $ok = false;
        }
        return $ok;
    }
}
