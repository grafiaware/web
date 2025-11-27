<?php

namespace Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider;

use Site\ConfigurationCache;

use FrontControler\PresentationFrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Mail\Mail;
use Mail\MessageFactory\HtmlMessage;

use Mail\Assembly;
use Mail\AssemblyInterface;

use Mail\Assembly\Host;
use Mail\Assembly\Encryption;
use Mail\Assembly\Smtp;
use Mail\Assembly\Party;
use Mail\Assembly\Content;
use Mail\Assembly\Attachment;
use Mail\Assembly\Headers;

use Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider\AssemblyProviderInterface;

use UnexpectedValueException;
use LogicException;

/**
 * 
 */
class AssemblyProvider implements AssemblyProviderInterface {

    
    private $htmlMessageFactory;

    public function __construct(
            HtmlMessage $htmlMessageFactory
            ) {        
        $this->htmlMessageFactory = $htmlMessageFactory;
    }

    public function getAssembly($assemblyName, $mailAdresata, $jmenoAdresata): AssemblyInterface {
        
        $data_logo_klic = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKUAAABHCAMAAABReXdhAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjYzNTgzQkM3NkFCODExRUI5REZDQzY4QjUwRTQxREY3IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjYzNTgzQkM4NkFCODExRUI5REZDQzY4QjUwRTQxREY3Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NjM1ODNCQzU2QUI4MTFFQjlERkNDNjhCNTBFNDFERjciIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NjM1ODNCQzY2QUI4MTFFQjlERkNDNjhCNTBFNDFERjciLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4+H/XYAAAAM1BMVEXMBx7ZRVblg47ywcf87/HPFyzSJjrsoqrfZHP54OPWNkjcVWX10NXvsbjidIHpkpz///8m6pr6AAAAEXRSTlP/////////////////////ACWtmWIAAAYTSURBVHjazJrrYqMgEIW539G8/9MuwwACamNr64Y/m26a+jkMZ86MIa+HF2eGwmLaXv8QeRQxGEm25bT4QEpLkU5BLDOu1B9HuQKYNLEE0Gqffqb2sygNMLJhj23ilOGTKM1h4KK8hPkUJUuQ5ug8XcJ8iJI3SL7AGaImbphOfAilS2CZiTYdUoVzJYR9BmVMVJCTGo650TwuCl5gDCmR4iMoPcYLYH0h0okTt5q/D+YjlAJDKWR/goSrPymiPoEyYlYmMfI9e4pmLOc/fAAly3taIjocfFX+XT+AMgWR58M8KSYtwTyW0qcpaQ6iL0zb0gVPoUz9f8rMQibFsemY4/vqYygJ2V2s/Bdt7wRG0w+SmtU+TukLJT1OhUYJGtp55OfPeDij5I2SIyM1LDUduYbqRynTKeEhBVTZY8q0x+iaCG1NRsymRDxImQQRXcZs0vBABcAxnQEpn0qfUeHBOp4RPThzO1dODDW810p8nyl4X4+5DRAbP8m3RiUCGHJoOdIvZL/0DGVIW7sIZsgojAapc0YSfZLR9BFKbvwqllR3eIZZ7HB4dKVkpbFkeaaw1FPk86/8OWXMmpJiyS3qYJeaCo844C/ZtrtNLku7mdyeeoCyCTV/ZW3p888XrRFcNL10SS5ZtvLokRlUf/L3IqTsqiRQBqp0LKW7dmZEGhwY2SxFTS55tfIWTOlfUzJMPfRuJQdVU82MBnWb5r1eJo/MsLGTP6IMfFiaDWuh/SpGiFWjG7wcUtOylovT0CgWQUg3YklKCs7jVxeilNxZcm5nl0k1bYRh4bIbwEn0IOkGOcmK+9tLthuUaDF4ZzUEeWd6m06FSineXgjXGG0Wx12355faUcaLlK6LZfq8//pCNw3wTBnc2zanhlw2Sn1Sm36tgtMhL/0k7F/aPV8+wQmrGvFXSkS3XreIvAtHsjGHshSm7O5I/BvKmrBL27a0Y3y166iK2OrgsF0NI1hfcsSCIEFextNr2e18TGLFPJ118WwVveRwbKSbHJp2/Yltb8EIHvOCQRbDGTcbiN5J1v2layzp7DZeAvsbw+DqvpXIsMjqf2G2BHVc7f7k5LHPlxvjacZor/nWfTW6DP9aP7UGBLlZufx4gCjX/x4j6ImW4cJsonS9KoYfJChMBhxRsG8OrdFQ5IeGTTTrRku0rKz+ssPYU9Lb5yhHQeaQ2GlwwPZjLJFsQeSimx6qnVefKekvUObcA0+xilnPG+VZTQHLFB6hzGJIPQZ0nE1HlCWgP3o8JeBD666HDDMlezulvewy88ZPei5wP3k5EpMpyt7d7PtxPVPGaS56Y5wVLNs3sy6rKRR5GBHJvnMLpjvM5GAgMggm+w1KBafmYMqPFwxQsUUOtlp0Nta58SGKH8yJNNlNGNnveJGsFaeUL9x4a8Yi0j20JM0vM3/wEE6ggLl4CzJ3WF9Q1smg0KbWGOl7745vx3IX+2dtgpLzycM3Gkk2OuFh/Ao7JkafYg9mweLIlGyehaWOSorblAc1Qm5G9/3EGoZGW4Ct91CG9Zix+j6lm0fWljT3eYHSD5VKHxiPa03AG8plhlnLdc27x1KkCUV/rnHeCN1zHi1BGOh9Sj7fqypyTAm58IyiJYzgLf65OBbgtczvf1zKy+lWY3qv9brkCmUWiohTkGWmdHn+xG5R1oTRw4PIIEsGvG99Sd2Qus8bJU7k7au8e+eQ0zLCol22A+RSnQ97TxnLnuYv+RQ0zJUcQvhj670ert4kSN6Cr5is8gz/eeGZLivDxR5toKQQ7juUocYQRgbEZwVuNYT1w8JvU8rykt6nTOemgIhlbhhL03CF0uJz1Q21nZ7fodRbP52KNXRyrYq496GslEWNTijlbUr41oP4smn4GaWE9tHAS/hqiLlLyU/UxpALX9SplDBJXV4v2uSoteJN2m/OadjBszHsbC7UXlIfduRbMhvl0l5yefQI8dvL7MdY+PWi1zXKV4R89gHadtbsENi83UjmbmNuNk6BI6JLHcuT3xLNXtvlziYyHJSra3n06Dduc8Hplrq6U89+Lzhts6+gbrme6Q9Toj389mDsnwADAGTPTF7l7xAbAAAAAElFTkSuQmCC" ;
        $data_logo_grafia="data:image/gif;base64,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" ;
        //base64_encode()               
        
        switch ($assemblyName) {
            case AssemblyProviderInterface::ASSEMBLY_ANKETA_2025:
                    $subject =  "Veletrh práce a vzdělávání - nabídka";                                        
                    $body = $this->htmlMessageFactory->create(__DIR__."/Messages/Pro ankety 2025.php",
                                                        ['doSestavy' => ' ..TOTO JE varianta..',
                                                         'data_logo_grafia' => $data_logo_grafia,
                                                         'data_logo_klic' => $data_logo_klic,   
                                                            
                                                        ]);
                    $attachments = [];                                                         
//                    $attachments = [
//                            (new Attachment())
//                            ->setFileName(ConfigurationCache::mail()['mail.attachments'].'nejakePdf.pdf')
//                            ->setAltText('pdf k rozeslání'), ];                            
                    $assembly = (new Assembly())
                        ->setContent(  (new Content())
                                     ->setSubject($subject)
                                     ->setHtml($body)
                                     ->setAttachments($attachments)
                                )
                        ->setParty  (  (new Party())
                                     ->setFrom('info@najdisi.cz', 'Veletrh Práce')    
                                     ->addTo( $mailAdresata, $jmenoAdresata )
                                  //   ->addCc( 'webmaster@grafia.cz', 'Kopie')    //kopie
                                 //    ->addBcc( $bccAddress, $bccName)              //skryta              
                                );
                                            
                break;
            
            
            case 'CHYBÍ KONSTANTA':
                    $subject =  "Předmět druheho mailu";  
                                    
                    $body = $this->htmlMessageFactory->create(__DIR__."/Messages/Dva.php",
                                                        ['doSestavy' => ' ..TOTO JE varianta Dva..',
                                                         'data_logo_grafia' => $data_logo_grafia,
                                                        ]);
                    $attachments = [];
//                    $attachments = [
//                                        (new Attachment())
//                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'Katalog veletrhPRACE.online 2021.pdf')
//                                        ->setAltText('Katalog veletrhPRACE.online 2021'), ];
                    $assembly = (new Assembly())
                        ->setContent(  (new Content())
                                     ->setSubject("Předmět druheho mailu")
                                     ->setHtml($body)
                                     ->setAttachments($attachments)
                                )
                        ->setParty  (  (new Party())
                                     ->setFrom('info@najdisi.cz', 'Veletrh Prace')    
                                     ->addTo( $mailAdresata, $jmenoAdresata )
                                 //    ->addCc( $ccAddress, $ccName)   
                                 //    ->addBcc( $bccAddress, $bccName)                            
                                );                         
                break;
            

            default:
                throw new UnexpectedValueException("Zvolena nenadefinovaná sestava.");
                break;
        }
        
        //----------------------------------------------------------------
      
        return $assembly;
    }
    
    
    
   
    
    }
    
    
    
    
