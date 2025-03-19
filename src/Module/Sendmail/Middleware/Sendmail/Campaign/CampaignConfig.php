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
     * hodnota z ValidityEnum
     */
    
    private $sourceCsvFilepath;
    private $validatedCsvFilepath;
    private $csvFileRowIdCallback;
    private $validationDegree;
    
    private $emailCallback;
    private $userCallback;
    private $filterCallback;
    private $assemblyName;

    public function getSourceCsvFilepath(): string {
        return $this->sourceCsvFilepath;
    }

    public function getValidatedCsvFilepath(): string {
        return $this->validatedCsvFilepath;
    }
    
    public function getCsvFileRowIdCallback(): callable {
        return $this->csvFileRowIdCallback;
    }

    public function setCsvFileRowIdCallback(callable $csvFileRowIdCallback): CampaignConfigInterface {
        $this->csvFileRowIdCallback = $csvFileRowIdCallback;
        return $this;
    }

    public function getValidationDegree(): int {
        return $this->validationDegree;
    }

    public function getEmailCallback(): callable {
        return $this->emailCallback;
    }

    public function getUserCallback(): callable {
        return $this->userCallback;
    }
    
    public function getSendingConditionCallback(): callable {
        return $this->filterCallback;
    }

    public function getAssemblyName(): string {
        return $this->assemblyName;
    }
    
    public function setSourceCsvFilepath(string $sourceCsvFilepath): CampaignConfigInterface {
        $this->sourceCsvFilepath = $sourceCsvFilepath;
        return $this;
    }

    public function setVerifiedCsvFilepath(string $verifiedCsvFilepath): CampaignConfigInterface {
        $this->validatedCsvFilepath = $verifiedCsvFilepath;
        return $this;
    }
    
    public function setValidationDegree(int $validationDegree): CampaignConfigInterface {
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

    public function setSendingConditionCallback(callable $conditionCallback): CampaignConfigInterface {
        $this->filterCallback = $conditionCallback;
        return $this;
    }

    public function setAssemblyName(string $assemblyName): CampaignConfigInterface {
        $this->assemblyName = $assemblyName;
        return $this;
    }
}
