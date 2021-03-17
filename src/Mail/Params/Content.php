<?php

namespace Mail\Params;

use Mail\Params\Attachment;

/**
 * Description of Content
 *
 * @author pes2704
 */
class Content {

    private $subject;

    private $body;

    private $attachments = [];

    public function getSubject() : string {
        return '=?utf-8?B?'.base64_encode($this->subject).'?=';
    }

    public function getBody(): string {
        return $this->body;
    }

    public function getAltBody() {
        // In case any of our lines are larger than 70 characters, we should use wordwrap()
        return wordwrap(strip_tags($this->body), 70, PHP_EOL);
    }

    /**
     *
     * @return Attachment iterable
     */
    public function getAttachments(): iterable {
        return $this->attachments;
    }

    public function setSubject(string $subject) {
        $this->subject = $subject;
        return $this;
    }

    public function setBody(string $body) {
        $this->body = $body;
        return $this;
    }

    public function setAttachments(iterable $attachments) {
        $this->attachments = $attachments;
        return $this;
    }


}
