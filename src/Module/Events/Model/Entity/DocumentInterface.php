<?php

namespace Events\Model\Entity;

use Pes\Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface DocumentInterface extends PersistableEntityInterface {

    public function getId();

    public function getContent();

    public function getDocumentFilename();

    public function getDocumentMimetype();

    public function setId($id): DocumentInterface;

    public function setContent( $content ): DocumentInterface;

    public function setDocumentFilename($document_filename): DocumentInterface;

    public function setDocumentMimetype($document_mimetype): DocumentInterface;

}
