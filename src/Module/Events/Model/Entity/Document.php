<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;

/**
 * Description of Document
 *
 * @author pes2704
 */
class Document extends EntityAbstract implements DocumentInterface {

    private $id;
    private $document;
    private $documentFilename;
    private $documentMimetype;

    public function getId() {
        return $this->id;
    }

    public function getDocument() {
        return $this->document;
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

    public function setDocument($document): DocumentInterface {
        $this->document = $document;
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
