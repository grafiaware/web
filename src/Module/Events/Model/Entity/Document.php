<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\DocumentInterface;

/**
 * Description of Document
 *
 * @author pes2704
 */
class Document extends PersistableEntityAbstract implements DocumentInterface {

    private $id;    //NOT NULL
    private $content;
    private $documentFilename;
    private $documentMimetype;

    public function getId() {
        return $this->id;
    }

    public function getContent(): ?DocumentInterface {
        return $this->content;
    }

    public function getDocumentFilename() {
        return $this->documentFilename;
    }

    public function getDocumentMimetype() {
        return $this->documentMimetype;
    }

    
    public function setId($id): DocumentInterface {
        $this->id = $id;
        return $this;
    }

    public function setContent( $content ): DocumentInterface {
        $this->content = $content;
        return $this;
    }

    public function setDocumentFilename($document_filename): DocumentInterface {
        $this->documentFilename = $document_filename;
        return $this;
    }

    public function setDocumentMimetype($document_mimetype): DocumentInterface {
        $this->documentMimetype = $document_mimetype;
        return $this;
    }



}
