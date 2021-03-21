<?php

namespace Mail\Params;

/**
 * Description of Party
 *
 * @author pes2704
 */
class Party {
    private $from;
    private $to;
    private $cc;
    private $bcc;
    private $replyTo;

    public function getFrom() {
        return $this->encodeNames($this->from);
    }

    public function getToArray() {
        return $this->encodeNames($this->to);
    }

    public function getCcArray() {
        return $this->encodeNames($this->cc);
    }

    public function getBccArray() {
        return $this->encodeNames($this->bcc);
    }

    public function getReplyTo() {
        return $this->encodeNames($this->replyTo);
    }

    public function setFrom($fromAddress, $fromName) {
        $this->from = [$fromAddress, $fromName];
        return $this;
    }

    public function addTo($toAddress, $toName = '') {
        $this->to[] = [$toAddress, $toName];
        return $this;
    }

    public function addCc($ccAddress, $ccName = '') {
        $this->cc[] = [$ccAddress, $ccName];
        return $this;
    }

    public function addBcc($bccAddress, $bccName = '') {
        $this->bcc[] =  [$bccAddress, $bccName];
        return $this;
    }

    public function addReplyTo($replyToAddress, $replyToName) {
        $this->replyTo[] = [$replyToAddress, $replyToName];
        return $this;
    }

    private function encodeNames($addressArray) {
        return $addressArray;
        $addr = [];
        foreach ($addressArray as $address) {
            $addr[] = [$address[0], '=?utf-8?B?'.base64_encode($address[1])];
        }
    }
}
