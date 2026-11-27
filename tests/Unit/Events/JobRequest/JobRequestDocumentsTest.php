<?php
declare(strict_types=1);

namespace Test\Unit\Events\JobRequest;

use Events\Model\Entity\Document;
use Events\Model\Entity\DocumentInterface;
use Events\Model\Entity\VisitorJobRequest;
use Events\Model\Entity\VisitorProfile;
use Events\Model\Repository\DocumentRepoInterface;
use Events\Service\JobRequest\DocumentUpload;
use Events\Service\JobRequest\JobRequestDocuments;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

/**
 * #174: dokumenty zájmu o pozici – nahraný soubor má přednost, jinak kopie z profilu; zájem má vždy vlastní kopii.
 * #175: kontrola nahraného souboru (typ, velikost, chyba uploadu).
 */
final class JobRequestDocumentsTest extends TestCase {

    private const HASH = 'abc';

    /** @var array<int, DocumentInterface> */
    private array $stored = [];
    private int $nextId = 100;

    private function repo(): DocumentRepoInterface {
        $repo = $this->createMock(DocumentRepoInterface::class);
        $repo->method('get')->willReturnCallback(fn($id) => $this->stored[(int) $id] ?? null);
        $repo->method('add')->willReturnCallback(function (DocumentInterface $d) {
            $d->setId($this->nextId);
            $this->stored[$this->nextId++] = $d;
        });
        return $repo;
    }

    private function upload(): DocumentUpload {
        return new DocumentUpload(['.pdf', '.docx'], 1000);
    }

    private function file(string $name, string $content, int $error = UPLOAD_ERR_OK): UploadedFileInterface {
        $file = $this->createMock(UploadedFileInterface::class);
        $file->method('getError')->willReturn($error);
        $file->method('getClientFilename')->willReturn($name);
        $file->method('getClientMediaType')->willReturn('application/pdf');
        $file->method('getSize')->willReturn(strlen($content));
        $file->method('moveTo')->willReturnCallback(fn($path) => file_put_contents($path, $content));
        return $file;
    }

    private function storedDocument(int $id, string $name, string $content): void {
        $d = (new Document())->setId($id);
        $d->setContent($content);
        $d->setDocumentFilename($name);
        $d->setDocumentMimetype('application/pdf');
        $this->stored[$id] = $d;
    }

    private function profile(?int $cv, ?int $letter): VisitorProfile {
        $p = new VisitorProfile();
        $p->setCvDocument($cv);
        $p->setLetterDocument($letter);
        return $p;
    }

    public function testCopiesDocumentsFromProfile(): void {
        $this->storedDocument(1, 'cv.pdf', 'CV');
        $this->storedDocument(2, 'dopis.pdf', 'DOPIS');
        $request = new VisitorJobRequest();
        $messages = (new JobRequestDocuments($this->repo(), $this->upload()))
            ->attach($request, $this->profile(1, 2), [], self::HASH);

        $this->assertSame([], $messages);
        $this->assertNotSame(1, $request->getCvDocument(), 'zájem má vlastní kopii, ne dokument profilu');
        $this->assertSame('CV', $this->stored[$request->getCvDocument()]->getContent());
        $this->assertSame('cv.pdf', $this->stored[$request->getCvDocument()]->getDocumentFilename());
        $this->assertSame('DOPIS', $this->stored[$request->getLetterDocument()]->getContent());
    }

    public function testUploadedFileReplacesProfileDocument(): void {
        $this->storedDocument(1, 'cv.pdf', 'CV');
        $request = new VisitorJobRequest();
        $files = [JobRequestDocuments::UPLOADED_KEY_CV . self::HASH => $this->file('nove-cv.pdf', 'NOVE')];
        (new JobRequestDocuments($this->repo(), $this->upload()))->attach($request, $this->profile(1, null), $files, self::HASH);

        $this->assertSame('NOVE', $this->stored[$request->getCvDocument()]->getContent());
        $this->assertSame('nove-cv.pdf', $this->stored[$request->getCvDocument()]->getDocumentFilename());
        $this->assertSame('CV', $this->stored[1]->getContent(), 'dokument profilu se nemění');
        $this->assertNull($request->getLetterDocument());
    }

    public function testRejectedUploadFallsBackToProfileWithMessage(): void {
        $this->storedDocument(1, 'cv.pdf', 'CV');
        $request = new VisitorJobRequest();
        $files = [JobRequestDocuments::UPLOADED_KEY_CV . self::HASH => $this->file('virus.exe', 'X')];
        $messages = (new JobRequestDocuments($this->repo(), $this->upload()))
            ->attach($request, $this->profile(1, null), $files, self::HASH);

        $this->assertCount(1, $messages);
        $this->assertStringContainsString('nepovolený typ', $messages[0]);
        $this->assertSame('CV', $this->stored[$request->getCvDocument()]->getContent());
    }

    public function testEmptyFileInputIsIgnored(): void {
        $request = new VisitorJobRequest();
        $files = [JobRequestDocuments::UPLOADED_KEY_LETTER . self::HASH => $this->file('', '', UPLOAD_ERR_NO_FILE)];
        $messages = (new JobRequestDocuments($this->repo(), $this->upload()))
            ->attach($request, $this->profile(null, null), $files, self::HASH);

        $this->assertSame([], $messages);
        $this->assertNull($request->getCvDocument());
        $this->assertNull($request->getLetterDocument());
    }

    public function testValidate(): void {
        $upload = $this->upload();
        $this->assertNull($upload->validate($this->file('CV.PDF', 'obsah')));
        $this->assertStringContainsString('příliš velký', (string) $upload->validate($this->file('cv.pdf', str_repeat('x', 1001))));
        $this->assertStringContainsString('prázdný', (string) $upload->validate($this->file('cv.pdf', '')));
        $this->assertStringContainsString('příliš velký', (string) $upload->validate($this->file('cv.pdf', 'x', UPLOAD_ERR_INI_SIZE)));
        $this->assertStringContainsString('nepodařilo', (string) $upload->validate($this->file('cv.pdf', 'x', UPLOAD_ERR_PARTIAL)));
    }
}
