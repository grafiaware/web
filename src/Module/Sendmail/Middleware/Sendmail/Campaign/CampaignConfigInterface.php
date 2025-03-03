<?php
namespace Sendmail\Middleware\Sendmail\Campaign;

/**
 *
 * @author pes2704
 */
interface CampaignConfigInterface {
    
    public function getSourceCsvFilepath();

    public function getValidatedCsvFilepath();

    public function getValidationDegree();

    public function getEmailCallback();

    public function getUserCallback();
    
    public function getFilterCallback();

    public function getContentAssembly();

    public function setSourceCsvFilepath($sourceCsvFilepath): CampaignConfigInterface;

    public function setVerifiedCsvFilepath($verifiedCsvFilepath): CampaignConfigInterface;

    public function setValidationDegree($validationDegree): CampaignConfigInterface;

    public function setEmailCallback(callable $emailCallback): CampaignConfigInterface;

    public function setUserCallback(callable $userCallback): CampaignConfigInterface;
    
    public function setFilterCallback($filterCallback): CampaignConfigInterface;

    public function setContentAssembly($contentAssembly): CampaignConfigInterface;
    
}
