<?php
namespace Sendmail\Middleware\Sendmail\Campaign;

use Sendmail\Middleware\Sendmail\Campaign\CampaignConfigInterface;
/**
 * Description of CampaignConfig
 *
 * @author pes2704
 */
class CampaignConfig implements CampaignConfigInterface {
    /**
     * parametry:
     * vstupní csv, verified csv, email callback => closure, která z řádky dat vrací email adresu příjemce, user callback => closure, která z řádky dat vrací jméno příjemce
     * 
     * stupeň verifikace:
     * hodnota z ValidationDegreeEnum
     */
    
    private $sourceCsvFilepath;
    private $validatedCsvFilepath;
    private $validationDegree;
    
    private $emailCallback;
    private $userCallback;
    private $filterCallback;
    private $contentAssembly;

    public function getSourceCsvFilepath() {
        return $this->sourceCsvFilepath;
    }

    public function getValidatedCsvFilepath() {
        return $this->validatedCsvFilepath;
    }
    
    public function getValidationDegree() {
        return $this->validationDegree;
    }

    public function getEmailCallback() {
        return $this->emailCallback;
    }

    public function getUserCallback() {
        return $this->userCallback;
    }
    
    public function getFilterCallback() {
        return $this->filterCallback;
    }

    public function getContentAssembly() {
        return $this->contentAssembly;
    }
    
    public function setSourceCsvFilepath($sourceCsvFilepath): CampaignConfigInterface {
        $this->sourceCsvFilepath = $sourceCsvFilepath;
        return $this;
    }

    public function setVerifiedCsvFilepath($verifiedCsvFilepath): CampaignConfigInterface {
        $this->validatedCsvFilepath = $verifiedCsvFilepath;
        return $this;
    }
    
    public function setValidationDegree($validationDegree): CampaignConfigInterface {
        $this->validationDegree = $validationDegree;
        return $this;
    }
    
    public function setEmailCallback(callable $emailCallback): CampaignConfigInterface {
        $this->emailCallback = $emailCallback;
        return $this;
    }

    public function setUserCallback(callable $userCallback): CampaignConfigInterface {
        $this->userCallback = $userCallback;
        return $this;
    }

    public function setFilterCallback($filterCallback): CampaignConfigInterface {
        $this->filterCallback = $filterCallback;
        return $this;
    }

    public function setContentAssembly($contentAssembly): CampaignConfigInterface {
        $this->contentAssembly = $contentAssembly;
        return $this;
    }
}
