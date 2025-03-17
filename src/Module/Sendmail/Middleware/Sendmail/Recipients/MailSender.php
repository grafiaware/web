<?php
namespace Sendmail\Middleware\Sendmail\Recipients;

use Sendmail\Middleware\Sendmail\Recipients\MailSenderInterface;
use Sendmail\Middleware\Sendmail\Campaign\Contents\MailContentInterface;
use Sendmail\Middleware\Sendmail\Csv\CampaignDataInterface;

use Sendmail\Middleware\Sendmail\Campaign\CampaignConfigInterface;
use Sendmail\Middleware\Sendmail\Recipients\MailRecipientsInterface;
use Mail\Mail;
use Mail\Exception\MailException;

/**
 * Description of MailSender
 *
 * @author pes2704
 */
class MailSender implements MailSenderInterface {
    
    private $mail;
    private $mailContent;
    private $campaignData;

    public function __construct(
            Mail $mail,
            MailContentInterface $mailContent,
            CampaignDataInterface $campaignData
            
            ) {
        $this->mail = $mail;
        $this->mailContent = $mailContent;
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
        $assembly = $campaignConfig->getContentAssembly();
        $emailCallback = $campaignConfig->getEmailCallback();
        $userCallback = $campaignConfig->getUserCallback();
        $sendingConditionCallback = $campaignConfig->getSendingConditionCallback();
        
        $targetData = $this->campaignData->importTargetCsvFile($campaignConfig);
        $this->mailContent->setAssembly($assembly);
        $report = [];
        foreach ($targetData as &$dataRow) {  // použití reference - umožňuje měnit data v poli v průběhu cyklu
            if ($sendingConditionCallback($dataRow)) {
                $mailAdresata = $emailCallback($dataRow);
                $jmenoAdresata = $userCallback($dataRow);
                $params = $this->mailContent->getParams($mailAdresata, $jmenoAdresata);
                try {
//                    $this->mail->mail($params);
//                    $result = 'Sended '.date("Y-m-d H:i:s");
                    $result = 'Test '.date("Y-m-d H:i:s");
                } catch (MailException $mailExc) {
                    $message = $mailExc->getMessage();
                    $errorInfo = $mailExc->getPrevious()->getMessage();
                    $result = $message.' Info: '.$errorInfo;
                }
                array_merge($dataRow, [
                    MailSenderInterface::MAIL_SENDED => $assembly,
                    MailSenderInterface::SENDING_TIME => date("Y-m-d H:i:s")
                ]);
                $report[] = [
                    'email' => $mailAdresata,
                    'name' => $jmenoAdresata,
                    'result' => $result
                ];                       
            }
        }
        return $report;
    }
    
}
