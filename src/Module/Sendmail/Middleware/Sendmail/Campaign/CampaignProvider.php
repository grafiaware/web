<?php
namespace Sendmail\Middleware\Sendmail\Campaign;

use Sendmail\Middleware\Sendmail\Campaign\CampaignProviderInterface;
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
    
    public function getCampaignConfig($name) {
        $campaignConfig = new CampaignConfig();
        $data = $this->configData($name);
        $campaignConfig->setSourceCsvFilepath($data['sourceCsvFilepath']);
        $campaignConfig->setVerifiedCsvFilepath($data['verifiedCsvFilepath']);
        $campaignConfig->setCsvFileRowIdCallback($data['csvFileRowIdCallback']);
        $campaignConfig->setEmailCallback($data['emailCallback']);
        $campaignConfig->setUserCallback($data['userCallback']);
        $campaignConfig->setValidationDegree($data['validationDegree']);
        $campaignConfig->setSendingConditionCallback($data['sendingConditionCallback']);
        $campaignConfig->setContentAssembly($name);
        return $campaignConfig;
    }
    
    private function configData($name) {
        switch ($name) {
            case self::JEDNA:
                $data = [
                    'sourceCsvFilepath' => ConfigurationCache::mail()['filesDirectory'] . "MujSoubor.csv",
                    'verifiedCsvFilepath' => ConfigurationCache::mail()['filesDirectory'] . "MujSoubor_validated.csv",
                    'csvFileRowIdCallback' => function($row) {return $row["Časová značka"];},
                    'emailCallback' => function($row) {return $row["E-mail:"];},
                    'userCallback' => function($row) {return isset($row["Příjmení:"]) ? ($row["Příjmení:"].' '.$row["Jméno:"]) : $row["E-mail:"];},
                    'validationDegree' => ValidityEnum::USER,
                    'sendingConditionCallback' => function($row) {return ($row[MailRecipientsInterface::MAIL_ADDRESS_VALIDITY]>= ValidityEnum::DOMAIN);},
                ];
                break;

            default:
                throw new UnexpectedValueException("Neznámé jméno konfigurace kampaně '$name'");
        }
        return $data;
    }
}
