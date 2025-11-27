<?php
namespace Mail;

use Mail\Assembly\Host;
use Mail\Assembly\Encryption;
use Mail\Assembly\Smtp;
use Mail\Assembly\Party;
use Mail\Assembly\Content;
use Mail\Assembly\Attachment;
use Mail\Assembly\Headers;


/**
 *
 * @author vlse2610
 */
interface AssemblyInterface {
    public function adoptConfigurationParams(AssemblyInterface $params);
    public function getHost(): ?Host;
    public function getSmtp(): ?Smtp;
    public function getParty(): ?Party;
    public function getContent(): ?Content;
    public function getHeaders(): ?Headers;
    public function setHost(Host $host);
    public function setSmtp(Smtp $smtpAuth);
    public function setParty(Party $party);
    public function setContent(Content $content);
    public function setHeaders(Headers $headers);
    
}
