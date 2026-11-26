<?php

namespace Events\Service\JobRequest;

use Events\Model\Entity\Document;
use Events\Model\Entity\DocumentInterface;
use Psr\Http\Message\UploadedFileInterface;
use Site\ConfigurationCache;

/**
 * #174, #175: kontrola nahraného souboru (životopis, motivační dopis) a vytvoření entity Document.
 * Povolené přípony z konfigurace `upload.events.acceptedextensions`, nejvyšší velikost `upload.events.maxsize`
 * (výchozí 10 MB; sloupec document.content je MEDIUMBLOB).
 */
class DocumentUpload {

    public const DEFAULT_MAX_SIZE = 10485760;

    /**
     * @param string[] $acceptedExtensions přípony s tečkou, např. ".pdf"
     */
    public function __construct(
        private array $acceptedExtensions,
        private int $maxSize = self::DEFAULT_MAX_SIZE,
    ) {
    }

    public static function fromConfiguration(): self {
        $conf = ConfigurationCache::eventsUploads();
        return new self(
            $conf['upload.events.acceptedextensions'] ?? [],
            (int) ($conf['upload.events.maxsize'] ?? self::DEFAULT_MAX_SIZE)
        );
    }

    /** @return string[] povolené přípony s tečkou (atribut accept formuláře) */
    public function acceptedExtensions(): array {
        return $this->acceptedExtensions;
    }

    public function maxSizeMb(): int {
        return (int) round($this->maxSize / 1048576);
    }

    /** Byl ve formuláři vybrán soubor? (prázdné pole input type=file posílá UPLOAD_ERR_NO_FILE) */
    public function isSent(?UploadedFileInterface $file): bool {
        return isset($file) && $file->getError() !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * @return string|null chybová hláška pro návštěvníka, null = soubor je v pořádku
     */
    public function validate(UploadedFileInterface $file): ?string {
        $name = (string) $file->getClientFilename();
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
                ? "Soubor $name je příliš velký."
                : "Soubor $name se nepodařilo nahrát.";
        }
        $ext = '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $accepted = array_map('strtolower', $this->acceptedExtensions);
        if ($accepted && !in_array($ext, $accepted, true)) {
            return "Soubor $name má nepovolený typ. Povolené typy: " . implode(', ', $this->acceptedExtensions) . '.';
        }
        $size = (int) $file->getSize();
        if ($size <= 0) {
            return "Soubor $name je prázdný.";
        }
        if ($size > $this->maxSize) {
            return "Soubor $name je příliš velký (nejvýše " . round($this->maxSize / 1048576) . ' MB).';
        }
        return null;
    }

    /** Nová (dosud neuložená) entita Document s obsahem nahraného souboru. */
    public function createDocument(UploadedFileInterface $file): DocumentInterface {
        $document = new Document();
        $this->fill($document, $file);
        return $document;
    }

    /** Naplní (i existující) Document obsahem nahraného souboru. */
    public function fill(DocumentInterface $document, UploadedFileInterface $file): void {
        $temp = tempnam(sys_get_temp_dir(), 'vpdoc');
        $file->moveTo($temp);
        $content = file_get_contents($temp);
        @unlink($temp);
        $document->setContent($content);
        $document->setDocumentFilename(basename((string) $file->getClientFilename()));
        $document->setDocumentMimetype($file->getClientMediaType());
    }

    /** Nová (dosud neuložená) kopie dokumentu – zájem o pozici má vlastní kopie, profil zůstává beze změny. */
    public function copy(DocumentInterface $source): DocumentInterface {
        $document = new Document();
        $document->setContent($source->getContent());
        $document->setDocumentFilename($source->getDocumentFilename());
        $document->setDocumentMimetype($source->getDocumentMimetype());
        return $document;
    }
}
