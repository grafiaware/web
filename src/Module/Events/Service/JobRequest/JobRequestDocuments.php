<?php

namespace Events\Service\JobRequest;

use Events\Model\Entity\VisitorJobRequestInterface;
use Events\Model\Entity\VisitorProfileInterface;
use Events\Model\Repository\DocumentRepoInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * #174: dokumenty zájmu o pozici (životopis, motivační dopis).
 *
 * Pro každý typ: soubor nahraný ve formuláři zájmu má přednost, jinak se zkopíruje dokument z profilu návštěvníka.
 * Zájem o pozici vždy dostane vlastní záznam v `document` (kopii) – pozdější změna nebo smazání v profilu
 * neovlivní, co už bylo zaměstnavateli odesláno. Nepřijatý soubor → hláška a kopie z profilu (pokud je).
 */
class JobRequestDocuments {

    public const UPLOADED_KEY_CV = 'visitor-cv';
    public const UPLOADED_KEY_LETTER = 'visitor-letter';

    public function __construct(
        private DocumentRepoInterface $documentRepo,
        private DocumentUpload $upload,
    ) {
    }

    /**
     * Nastaví cv_document a letter_document zájmu o pozici. Volat před uložením zájmu (repo->add).
     *
     * @param UploadedFileInterface[] $uploadedFiles z $request->getUploadedFiles(), klíče UPLOADED_KEY_* . $userHash
     * @return string[] hlášky pro návštěvníka (nepřijaté soubory)
     */
    public function attach(VisitorJobRequestInterface $jobRequest, ?VisitorProfileInterface $profile,
            array $uploadedFiles, string $userHash): array {
        $messages = [];
        $cvId = $this->documentId($uploadedFiles[self::UPLOADED_KEY_CV . $userHash] ?? null,
                $profile?->getCvDocument(), $messages);
        $letterId = $this->documentId($uploadedFiles[self::UPLOADED_KEY_LETTER . $userHash] ?? null,
                $profile?->getLetterDocument(), $messages);
        $jobRequest->setCvDocument($cvId);
        $jobRequest->setLetterDocument($letterId);
        return $messages;
    }

    private function documentId($file, $profileDocumentId, array &$messages) {
        if ($file instanceof UploadedFileInterface && $this->upload->isSent($file)) {
            $error = $this->upload->validate($file);
            if ($error === null) {
                $document = $this->upload->createDocument($file);
                $this->documentRepo->add($document);   // autoincrement – uloží se hned a dostane id
                return $document->getId();
            }
            $messages[] = $error . (isset($profileDocumentId) ? ' Přiložen soubor z vašeho profilu.' : '');
        }
        if (isset($profileDocumentId)) {
            $source = $this->documentRepo->get($profileDocumentId);
            if (isset($source)) {
                $copy = $this->upload->copy($source);
                $this->documentRepo->add($copy);
                return $copy->getId();
            }
        }
        return null;
    }
}
