<?php

namespace Mail;

use Mail\AssemblyInterface;
use Mail\Assembly\Host;
use Mail\Assembly\Encryption;
use Mail\Assembly\SmtpAuth;
use Mail\Assembly\Party;
use Mail\Assembly\Content;
use Mail\Assembly\Attachment;
use Mail\Assembly\Headers;


/**
 * Description of Configuration
 *
 * @author pes2704
 */
class Assembly implements AssemblyInterface {

    /**
     *
     * @var Host
     */
    private $host;

    /**
     *
     * @var SmtpAuth
     */
    private $smtpAuth;

    /**
     *
     * @var Encryption
     */
    private $encryption;

    /**
     *
     * @var Party
     */
    private $party;

    /**
     *
     * @var Content
     */
    private $content;

    /**
     *
     * @var Headers
     */
    private $headers;

    public function adoptConfigurationParams(AssemblyInterface $params) {
        if ($params->getContent()) {
            $this->setContent($params->getContent());
        }
        if ($params->getEncryption()) {
            $this->setEncryption($params->getEncryption());
        }
        if ($params->getHeaders()) {
            $this->setHeaders($params->getHeaders());
        }
        if ($params->getHost()) {
            $this->setHost($params->getHost());
        }
        if ($params->getParty()) {
            $this->setParty($params->getParty());
        }
        if ($params->getSmtpAuth()) {
            $this->setSmtpAuth($params->getSmtpAuth());
        }

    }

    public function getHost(): ?Host {
        return $this->host;
    }

    public function getSmtpAuth(): ?SmtpAuth {
        return $this->smtpAuth;
    }

    public function getEncryption(): ?Encryption {
        return $this->encryption;
    }

    public function getParty(): ?Party {
        return $this->party;
    }

    public function getContent(): ?Content {
        return $this->content;
    }

    public function getHeaders(): ?Headers {
        return $this->headers;
    }

    public function setHost(Host $host) {
        $this->host = $host;
        return $this;
    }

    public function setSmtpAuth(SmtpAuth $smtpAuth) {
        $this->smtpAuth = $smtpAuth;
        return $this;
    }

    public function setEncryption(Encryption $encryption) {
        $this->encryption = $encryption;
        return $this;
    }

    public function setParty(Party $party) {
        $this->party = $party;
        return $this;
    }

    public function setContent(Content $content) {
        $this->content = $content;
        return $this;
    }

    public function setHeaders(Headers $headers) {
        $this->headers = $headers;
        return $this;
    }

}
