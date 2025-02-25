<?php

namespace Sendmail\Middleware\Sendmail\Controler\Contents;

use Site\ConfigurationCache;


use Psr\Http\Message\ServerRequestInterface;

use Mail\Mail;
use Mail\MessageFactory\HtmlMessage;

use Mail\Params;
use Mail\Params\Content;
use Mail\Params\Attachment;
use Mail\Params\Party;

use Sendmail\Middleware\Sendmail\Controler\Contents\MailContentInterface;

use Sendmail\Middleware\Sendmail\Controler\Contents\MailRecipientsInterface;

use UnexpectedValueException;



/**
 * 
 */
class MailRecipients implements MailRecipientsInterface {



    public function __construct(
            
//            StatusSecurityRepo $statusSecurityRepo,
//            StatusFlashRepo $statusFlashRepo,
//            StatusPresentationRepo $statusPresentationRepo,
//            AccessPresentationInterface $accessPresentation,
//            LoginAggregateCredentialsRepo $loginAggregateCredentialsRepo,
//            RegistrationRepo $registrationRepo
            ) {
//        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $accessPresentation);
//        $this->loginAggregateCredentialsRepo = $loginAggregateCredentialsRepo;
//        $this->registrationRepo = $registrationRepo;
        
        
      //  $this->htmlMessageFactory = $htmlMessage;
    }
    
    


    public function getRecipients() {
        //$data = importCsv("VPV_ankety_test.csv");

        
        
        // ------------------     tady nebude ------------------------
        //$verifiedData = [];
        //foreach ($data as $dataRow) {
        //    $email = $dataRow['E-mail:'];
        //    if (is_string($email)) {
        //        $test = verifyEmail($email);
        //        $verified = $test['verified'];
        //    } else {
        //        $verified = 'no mail';
        //    }
        //    $verifiedData[] = array_merge($dataRow, ['mail verified' => $verified, 'verification time'=> date("Y-m-d H:i:s")]);
        
        //exportCsv("VPV_ankety_test_verified.csv", $verifiedData);

        
        
        //  tady vybrat z dat ty user
        
        
        
        
        
        return $verifiedData;
        
    
    }
    
    
    
   
    
    }
    
    
    
    
