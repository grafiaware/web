<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use Events\Model\Entity\DocumentInterface;

/**
 *
 * @author pes2704
 */
interface DocumentInterface extends PersistableEntityInterface {

    public function getId();

    public function getDocument(): ?DocumentInterface;

    public function getDocumentFilename();

    public function getDocumentMimetype();

    public function setId($id): DocumentInterface;

    public function setDocument( $document  ): DocumentInterface;

    public function setDocumentFilename($document_filename): DocumentInterface;

    public function setDocumentMimetype($document_mimetype): DocumentInterface;

}
