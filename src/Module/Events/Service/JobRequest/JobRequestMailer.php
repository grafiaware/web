<?php

namespace Events\Service\JobRequest;

use Events\Model\Entity\JobInterface;
use Events\Model\Entity\VisitorJobRequestInterface;
use Events\Model\Repository\DocumentRepoInterface;
use Mail\MailSettings;
use Mail\MessageFactory\HtmlMessage;
use Pes\Mail\Assembly;
use Pes\Mail\Assembly\Content;
use Pes\Mail\Assembly\StringAttachment;
use Pes\Mail\MailInterface;

/**
 * Zájem o pozici – e-maily:
 * - zástupci firmy: údaje zájemce + přílohy (životopis, motivační dopis) – při odeslání zájmu (#173)
 *   a na vyžádání zástupce „odeslat na svůj e-mail“ (#172),
 * - návštěvníkovi: potvrzení odeslání zájmu (#173).
 * Adresy jsou z registrace (auth `registration.email`), odesílatel z MailSettings ('info').
 */
class JobRequestMailer {

    private const MESSAGES = __DIR__ . '/../../Middleware/Events/Controler/Messages/';
    private const SUBJECT_PREFIX = 'Veletrh práce a vzdělávání - ';
    private const SENDER = 'info';

    public function __construct(
        private MailInterface $mail,
        private HtmlMessage $messageFactory,
        private MailSettings $settings,
        private DocumentRepoInterface $documentRepo,
    ) {
    }

    public function sendToRepresentative(VisitorJobRequestInterface $jobRequest, JobInterface $job, string $email, string $name): void {
        $body = $this->messageFactory->create(self::MESSAGES . 'pracovni-udaje-navstevnika.php', [
            'data_logo_grafia' => $this->settings->logoDataUri('grafia'),
            'visitorJobRequest' => $jobRequest,
            'job' => $job,
        ]);
        $this->send(self::SUBJECT_PREFIX . 'zájemce o pozici ' . $job->getNazev() . '.', $body,
                $this->attachments($jobRequest), $email, $name);
    }

    public function sendConfirmationToVisitor(VisitorJobRequestInterface $jobRequest, JobInterface $job, string $email): void {
        $body = $this->messageFactory->create(self::MESSAGES . 'potvrzeni-zajmu-o-pozici.php', [
            'data_logo_grafia' => $this->settings->logoDataUri('grafia'),
            'visitorJobRequest' => $jobRequest,
            'job' => $job,
            'documentNames' => $this->documentNames($jobRequest),
        ]);
        $name = trim($jobRequest->getName() . ' ' . $jobRequest->getSurname());
        $this->send(self::SUBJECT_PREFIX . 'odeslaný zájem o pozici ' . $job->getNazev() . '.', $body, [], $email, $name);
    }

    private function send(string $subject, string $body, array $attachments, string $email, string $name): void {
        $this->mail->mail((new Assembly())
            ->setContent((new Content())->setSubject($subject)->setHtml($body)->setAttachments($attachments))
            ->setParty($this->settings->party(self::SENDER)->addTo($email, $name)));
    }

    /** @return StringAttachment[] */
    private function attachments(VisitorJobRequestInterface $jobRequest): array {
        $attachments = [];
        foreach ($this->documents($jobRequest) as $label => $document) {
            $attachments[] = (new StringAttachment())
                ->setStringAttachment($document->getContent())
                ->setAltText($label . ' - ' . $document->getDocumentFilename());   // PHPMailer: název souboru přílohy
        }
        return $attachments;
    }

    /** @return string[] popis => název souboru */
    private function documentNames(VisitorJobRequestInterface $jobRequest): array {
        return array_map(fn($document) => (string) $document->getDocumentFilename(), $this->documents($jobRequest));
    }

    private function documents(VisitorJobRequestInterface $jobRequest): array {
        $documents = [];
        foreach (['Životopis' => $jobRequest->getCvDocument(), 'Motivační dopis' => $jobRequest->getLetterDocument()] as $label => $id) {
            $document = isset($id) ? $this->documentRepo->get($id) : null;
            if (isset($document) && (string) $document->getContent() !== '') {
                $documents[$label] = $document;
            }
        }
        return $documents;
    }
}
