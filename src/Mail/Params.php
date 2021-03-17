<?php

namespace Mail;

use Mail\Params\{
    Host, Encryption, SmtpAuth, Party, Content, Attachment, Headers
};


/**
 * Description of Configuration
 *
 * @author pes2704
 */
class Params {

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

    public function getHost(): Host {
        return $this->host;
    }

    public function getSmtpAuth(): SmtpAuth {
        return $this->smtpAuth;
    }
    
    public function getEncryption(): Encryption {
        return $this->encryption;
    }

    public function getParty(): Party {
        return $this->party;
    }

    public function getContent(): Content {
        return $this->content;
    }

    public function getHeaders(): Headers {
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
