<?php
namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\DocumentInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class DocumentHydrator extends TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param DocumentInterface $document
     * @param RowDataInterface $rowData
     */
    public function hydrate(EntityInterface $document, RowDataInterface $rowData) {
        /** @var DocumentInterface $document */
        $document
                ->setId( $this->getPhpValue  ( $rowData, 'id'))
                ->setDocument( $this->getPhpValue ( $rowData, 'document'))
                ->setDocumentFilename( $this->getPhpValue ( $rowData, 'document_filename'))
                ->setDocumentMimetype( $this->getPhpValue ( $rowData, 'document_mimetype'));       
    }

    /**
     *
     * @param DocumentInterface $document
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $document, RowDataInterface $rowData) {
        /** @var DocumentInterface $document */
            // id je autoincrement
            $this->setSqlValue( $rowData, 'document', $document->getDocument());
            $this->setSqlValue( $rowData, 'document_filename', $document->getDocumentFilename());
            $this->setSqlValue( $rowData, 'document_mimetype', $document->getDocumentMimetype());            
    }

}
