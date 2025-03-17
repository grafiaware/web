<?php
namespace Sendmail\Middleware\Sendmail\Campaign;

/**
 *
 * @author pes2704
 */
interface CampaignConfigInterface {
    
    public function getSourceCsvFilepath(): string;

    public function getValidatedCsvFilepath(): string;
    
    public function getCsvFileRowIdCallback(): callable;

    public function getValidationDegree(): int;

    public function getEmailCallback(): callable;

    public function getUserCallback(): callable;
    
    public function getSendingConditionCallback(): callable;

    public function getContentAssembly(): string;

    public function setSourceCsvFilepath(string $sourceCsvFilepath): CampaignConfigInterface;

    public function setVerifiedCsvFilepath(string $verifiedCsvFilepath): CampaignConfigInterface;

    public function setCsvFileRowIdCallback(callable $csvFileRowIdCallback): CampaignConfigInterface;
    
    public function setValidationDegree(int $validationDegree): CampaignConfigInterface;

    public function setEmailCallback(callable $emailCallback): CampaignConfigInterface;

    public function setUserCallback(callable $userCallback): CampaignConfigInterface;
    
    public function setSendingConditionCallback(callable $conditionCallback): CampaignConfigInterface;

    public function setContentAssembly(string $contentAssembly): CampaignConfigInterface;
    
}
