<?php
namespace Mail;

use Mail\Assembly\Host;
use Mail\Assembly\Encryption;
use Mail\Assembly\SmtpConnection;
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
    public function getSmtp(): ?SmtpConnection;
    public function getParty(): ?Party;
    public function getContent(): ?Content;
    public function getHeaders(): ?Headers;
    public function setHost(Host $host): AssemblyInterface;
    public function setSmtp(SmtpConnection $smtpAuth): AssemblyInterface;
    public function setParty(Party $party): AssemblyInterface;
    public function setContent(Content $content): AssemblyInterface;
    public function setHeaders(Headers $headers): AssemblyInterface;
    
}
