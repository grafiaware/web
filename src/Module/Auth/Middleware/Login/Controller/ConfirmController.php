<?php
namespace Auth\Middleware\Login\Controller;

use Site\ConfigurationCache;
use Access\Enum\RoleEnum;

use Mail\Mail;
use Mail\MessageFactory\HtmlMessage;
use Mail\Params;
use Mail\Params\{Content, Attachment, Party};

use Psr\Http\Message\ServerRequestInterface;

use Pes\Security\Password\Password;

// model
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;

use Auth\Model\Repository\RegistrationRepo;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;

use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateCredentials;
use Auth\Model\Entity\Registration;

/**
 * Description of ConfirmController
 *
 * @author pes2704
 */
class ConfirmController extends LoginControllerAbstract
{
    /**
     *
     * @var LoginAggregateCredentialsRepo
     */
    private $loginAggregateCredentialsRepo;
    /**
     *
     * @var RegistrationRepo
     */
    private $registrationRepo;


    /**
     *
     */
    public function __construct(
                StatusSecurityRepo $statusSecurityRepo,
                   StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
     LoginAggregateCredentialsRepo $loginAggregateCredentialsRepo,
                  RegistrationRepo $registrationRepo )
    {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateCredentialsRepo = $loginAggregateCredentialsRepo;
        $this->registrationRepo = $registrationRepo;
    }

    public function confirm(ServerRequestInterface $request, $uid) {
            if ( $uid ) {
                /** @var  Registration $registrationEntity  */
                $registrationEntity = $this->registrationRepo->getByUid($uid);

                if ( isset($registrationEntity) ) {
                    $password = $registrationEntity->getPasswordHash();  // v registration je nezahash. heslo
                    $loginNameFk = $registrationEntity->getLoginNameFk();
                    $registerEmail = $registrationEntity->getEmail();

                    /** @var  LoginAggregateCredentials $loginAggregateCredentialsEntity  */
                    $loginAggregateCredentialsEntity = $this->loginAggregateCredentialsRepo->get($loginNameFk);
                    if ( $loginAggregateCredentialsEntity->getCredentials() === \NULL  ) {

                        // nové Credentials - se zahashovaným heslem, default rolí z konfigurace
                        $credentials = new Credentials();
                        $credentials->setPasswordHash( (new Password())->getPasswordHash($password) );
                        $credentials->setLoginNameFk($loginNameFk);
                        $credentials->setRoleFk(RoleEnum::VISITOR);
                        $loginAggregateCredentialsEntity->setCredentials($credentials);

                        // vymazání hesla z registrace
                        $registrationEntity->setPasswordHash('');

                        $this->addFlashMessage( "Potvrzeno, Vaše registrace byla dokončena.");

                        #########################--------- poslat mail -------------------
                        /** @var Mail $mail */
                        $mail = $this->container->get(Mail::class);
                        /** @var HtmlMessage $mailMessageFactory */
                        $mailMessageFactory = $this->container->get(HtmlMessage::class);

                        $subject =  'Veletrh práce a vzdělávání - Registrace dokončena.';
                        $body = $mailMessageFactory->create(__DIR__."/Messages/confirm.php", []);

                        $attachments = [ (new Attachment())
                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'logo_grafia.png')
                                        ->setAltText('Logo Grafia')
                                       ];
                        $params = (new Params())
                                    ->setContent(  (new Content())
                                                 ->setSubject($subject)
                                                 ->setHtml($body)
             //                                    ->setAttachments($attachments)
                                                 )
                                    ->setParty  (  (new Party())
                                                 ->setFrom('it.grafia@gmail.com', 'veletrhprace.online')
                                                 ->addReplyTo('svoboda@grafia.cz', 'reply veletrhprace.online')
                                                 ->addTo( $registerEmail, $loginNameFk)
                                                );
                        $mail->mail($params); // posle mail
                    }
                    else {
                        // stav, kdy -  Již bylo zaregistrováno a potvrzeno.
                        // nehlasit nic,        ?? nebo az po 10sec  poslat    **mail** ??? ??
                        $this->addFlashMessage( "Byli jste zaregistrováni.");
                    }
                 }
                 else {
                     // stav, kdy -  Takový registrační požadavek nebyl požadovan/zaznamenán.
                     // zapsat do logu
                     $this->addFlashMessage( "Takový registrační požadavek nebyl požadován/zaznamenán.");
                 }
            }

//        return $this->redirectSeeOther($request, 'web/v1/static/dokonceni-registrace'); // 303 See Other
        return $this->createResponseRedirectSeeOther($request, ''); // 303 See Other

    }
}