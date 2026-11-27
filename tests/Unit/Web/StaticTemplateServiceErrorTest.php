<?php
declare(strict_types=1);

namespace Test\Unit\Web;

use Component\Renderer\Html\StaticTemplateServiceError;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

/**
 * #191: hláška pro službu, kterou kontejner modulu položky menu nemá (static stránka založená s jiným modulem).
 */
final class StaticTemplateServiceErrorTest extends TestCase {

    private function notFound(string $service): \Throwable {
        return new class("Volání služby kontejneru get('$service') selhalo. Požadovaná služba kontejneru se jménem: '$service' neexistuje, nebyla nastavena. Kontejner Configured by X.")
            extends \LogicException implements NotFoundExceptionInterface {};
    }

    /** PhpTemplateRenderer obaluje výjimku z každé vnořené šablony. */
    private function wrapped(\Throwable $e): \Throwable {
        return new \RuntimeException('šablona program.php', 0, new \RuntimeException('šablona template.php', 0, $e));
    }

    public function testNamesServiceAndModuleForWrongMenuItemModule(): void {
        $service = 'Events\Middleware\Events\ViewModel\ProgramViewModel';
        $message = StaticTemplateServiceError::message($this->wrapped($this->notFound($service)), 'red');

        $this->assertNotNull($message);
        $this->assertStringContainsString($service, $message);
        $this->assertStringContainsString('kontejner modulu „red“', $message);
        $this->assertStringContainsString('statickou stránku modulu „events“', $message);
    }

    public function testNullApiModuleMeansRed(): void {
        $message = StaticTemplateServiceError::message($this->notFound('Auth\Model\Repository\LoginRepo'), null);
        $this->assertStringContainsString('kontejner modulu „red“', (string) $message);
        $this->assertStringContainsString('modulu „auth“', (string) $message);
    }

    public function testSameModuleOrUnknownNamespaceHasNoModuleHint(): void {
        $same = StaticTemplateServiceError::message($this->notFound('Events\Model\Repository\NeexistujeRepo'), 'events');
        $this->assertStringContainsString('Events\Model\Repository\NeexistujeRepo', (string) $same);
        $this->assertStringNotContainsString('je třeba založit', (string) $same);

        $alias = StaticTemplateServiceError::message($this->notFound('companyFamilyjobList'), 'red');
        $this->assertStringContainsString('companyFamilyjobList', (string) $alias);
        $this->assertStringNotContainsString('je třeba založit', (string) $alias);
    }

    public function testOtherExceptionsAreNotHandled(): void {
        $this->assertNull(StaticTemplateServiceError::message($this->wrapped(new \LogicException('jiná chyba')), 'red'));
    }

    public function testModuleOfService(): void {
        $this->assertSame('events', StaticTemplateServiceError::moduleOfService('\Events\Service\Lottery\LotteryService'));
        $this->assertSame('red', StaticTemplateServiceError::moduleOfService('Red\Model\Repository\MenuItemRepo'));
        $this->assertNull(StaticTemplateServiceError::moduleOfService('Component\ViewModel\StatusViewModel'));
        $this->assertNull(StaticTemplateServiceError::moduleOfService('rendererContainer'));
    }
}
