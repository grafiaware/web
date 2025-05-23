<?php
namespace Sendmail\Middleware\Sendmail\Recipients;

use Sendmail\Middleware\Sendmail\Recipients\MailSenderInterface;
use Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider\AssemblyProviderInterface;
use Sendmail\Middleware\Sendmail\Csv\CampaignDataInterface;

use Sendmail\Middleware\Sendmail\Campaign\CampaignConfigInterface;
use Sendmail\Middleware\Sendmail\Recipients\MailRecipientsInterface;
use Mail\Mail;
use Pes\Debug\Timer;
use Mail\Exception\MailException;

/**
 * Description of MailSender
 *
 * @author pes2704
 */
class MailSender implements MailSenderInterface {
    
    private $mail;
    private $assemblyProvider;
    private $campaignData;

    public function __construct(
            Mail $mail,
            AssemblyProviderInterface $assemblyProvider,
            CampaignDataInterface $campaignData
            
            ) {
        $this->mail = $mail;
        $this->assemblyProvider = $assemblyProvider;
        $this->campaignData = $campaignData;        
        
    }
    
    /**
     * 
     * @param CampaignConfigInterface $campaignConfig
     * @param type $maxRuntime Čas v sekundách. Největší čas, do kterého muší začít odesílání e-mailu. Pak se odesílání zastaví a metoda končí.
     * @param type $maxQuantity Nejvyšší počet pokusů o edslání e-mailu. Po dasažená počtu se odesílání zastaví a metoda končí.
     * @return array Report
     */
    public function sendEmails(CampaignConfigInterface $campaignConfig, $maxRuntime=10, $maxQuantity=50): array {
        $assemblyName = $campaignConfig->getAssemblyName();
        $emailCallback = $campaignConfig->getEmailCallback();
        $userCallback = $campaignConfig->getUserCallback();
        $sendingConditionCallback = $campaignConfig->getSendingConditionCallback();
        
        $targetData = $this->campaignData->importTargetCsvFile($campaignConfig);
        $report = [];
        $timer = new Timer();
        $startSeconds = $timer->start();
        $attempts=0;
        foreach ($targetData as &$dataRow) {  // použití reference - umožňuje měnit data v poli v průběhu cyklu
            if ($timer->runtime()>$maxRuntime || $attempts>$maxQuantity) {
                break;
            }
            if ($dataRow[MailSenderInterface::CAMPAIGN_ASSEMBLY]==='' && $sendingConditionCallback($dataRow)) {
                $mailAdresata = $emailCallback($dataRow);
                $jmenoAdresata = $userCallback($dataRow);
                $assembly = $this->assemblyProvider->getAssembly($assemblyName, $mailAdresata, $jmenoAdresata);
                try {
                    $this->mail->mail($assembly);
                    $result = 'Sended '.date("Y-m-d H:i:s");
//                    $result = 'Test '.date("Y-m-d H:i:s");
                    $attempts++;
                } catch (MailException $mailExc) {
                    $message = $mailExc->getMessage();
                    $errorInfo = $mailExc->getPrevious()->getMessage();
                    $result = $message.' Info: '.$errorInfo;
                }
                $dataRow[MailSenderInterface::CAMPAIGN_ASSEMBLY] = $assemblyName;
                $dataRow[MailSenderInterface::SENDING_TIME] = date("Y-m-d H:i:s");
                $report[] = [
                    'email' => $mailAdresata,
                    'name' => $jmenoAdresata,
                    'result' => $result
                ];                       
            }
        }
        $this->campaignData->exportTargetCsvFile($campaignConfig, $targetData);
        
        return $report;
    }
    
}
