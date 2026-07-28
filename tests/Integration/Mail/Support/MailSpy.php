<?php
declare(strict_types=1);

namespace Test\Integration\Mail\Support;

use Pes\Mail\AssemblyInterface;
use Pes\Mail\MailInterface;

final class MailSpy implements MailInterface
{
    /** @var list<AssemblyInterface|null> */
    public array $sent = [];

    public function mail(?AssemblyInterface $assembly = null): bool
    {
        $this->sent[] = $assembly;
        return true;
    }

    public static function actionOnSend(
        bool $result,
        array $to,
        array $cc,
        array $bcc,
        string $subject,
        string $body,
        string $from,
        array $extra
    ): void {
    }
}
