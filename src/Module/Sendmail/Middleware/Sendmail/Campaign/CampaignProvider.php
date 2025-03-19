<?php
namespace Sendmail\Middleware\Sendmail\Campaign;

use Sendmail\Middleware\Sendmail\Campaign\CampaignProviderInterface;
use Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider\AssemblyProviderInterface;
use Sendmail\Middleware\Sendmail\Recipients\ValidityEnum;
use Sendmail\Middleware\Sendmail\Recipients\MailRecipientsInterface;

use Site\ConfigurationCache;

use UnexpectedValueException;

/**
 * Description of CampaignProvider
 *
 * @author pes2704
 */
class CampaignProvider implements CampaignProviderInterface {
    
    public function getCampaignConfig($campaignName) {
        $campaignConfig = new CampaignConfig();
        $data = $this->configData($campaignName);
        $campaignConfig->setSourceCsvFilepath($data['sourceCsvFilepath']);
        $campaignConfig->setVerifiedCsvFilepath($data['verifiedCsvFilepath']);
        $campaignConfig->setCsvFileRowIdCallback($data['csvFileRowIdCallback']);
        $campaignConfig->setEmailCallback($data['emailCallback']);
        $campaignConfig->setUserCallback($data['userCallback']);
        $campaignConfig->setValidationDegree($data['validationDegree']);
        $campaignConfig->setSendingConditionCallback($data['sendingConditionCallback']);
        $campaignConfig->setAssemblyName($data['assemblyName']);
        return $campaignConfig;
    }
    
    private function configData($campaignName) {
        switch ($campaignName) {
            case self::JEDNA:
                $data = [
                    'sourceCsvFilepath' => ConfigurationCache::mail()['filesDirectory'] . "SouborVS.csv",
                    'verifiedCsvFilepath' => ConfigurationCache::mail()['filesDirectory'] . "MujSouborVS_validated.csv",
                    'csvFileRowIdCallback' => function($row) {return $row["Časová značka"];},
                    'emailCallback' => function($row) {return $row["E-mail:"];},
                    'userCallback' => function($row) {return isset($row["Příjmení:"]) ? ($row["Příjmení:"].' '.$row["Jméno:"]) : $row["E-mail:"];},
                    'validationDegree' => ValidityEnum::DOMAIN,
                    'sendingConditionCallback' => function($row) {return ($row[MailRecipientsInterface::MAIL_ADDRESS_VALIDITY]>= ValidityEnum::DOMAIN);},
                    'assemblyName' => AssemblyProviderInterface::ASSEMBLY_ANKETA_2025,
                ];
                break;

            default:
                throw new UnexpectedValueException("Neznámé jméno konfigurace kampaně '$campaignName'");
        }
        return $data;
    }
}
