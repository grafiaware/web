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

    private $html;

    private $attachments = [];

    public function getSubject() : string {
        return '=?utf-8?B?'.base64_encode($this->subject).'?=';
    }

    public function getHtml(): string {
        return $this->html;
    }

    public function getAltBody() {
        // In case any of our lines are larger than 70 characters, we should use wordwrap()
        return "Content without HTML: ".wordwrap(strip_tags($this->html), 70, PHP_EOL);
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

    public function setHtml(string $html) {
        $this->html = $html;
        return $this;
    }

    public function setAttachments(iterable $attachments) {
        $this->attachments = $attachments;
        return $this;
    }


}
