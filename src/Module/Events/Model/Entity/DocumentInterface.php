<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface DocumentInterface extends PersistableEntityInterface {

    public function getId();

    public function getDocument();

    public function getDocumentFilename();

    public function getDocumentMimetype();

    public function setId($id): DocumentInterface;

    public function setDocument($document): DocumentInterface;

    public function setDocumentFilename($document_filename): DocumentInterface;

    public function setDocumentMimetype($document_mimetype): DocumentInterface;

}
