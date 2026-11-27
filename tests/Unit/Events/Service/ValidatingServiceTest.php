<?php
declare(strict_types=1);

namespace Test\Unit\Events\Service;

use Auth\Model\Entity\LoginAggregateFull;
use Events\Service\ConnectionException;
use Events\Service\LoginServiceInterface;
use Events\Service\ValidatingException;
use Events\Service\ValidatingService;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Entity\Flash;
use Status\Model\Entity\Security;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

final class ValidatingServiceTest extends TestCase
{
    public function testConnectionFailureKeepsSessionAndAddsEventsLogin(): void
    {
        $security = $this->securityWithUser('editor');
        $flash = new Flash();
        $loginService = $this->createMock(LoginServiceInterface::class);
        $loginService->expects($this->once())->method('setAddUserNameToEventsLogin')->with('editor');
        $loginService->expects($this->never())->method('setDeleteUserNameFromEventsLogin');

        $service = $this->service($security, $flash, $loginService, new ConnectionException('Spojeni se nezdařilo.'));
        $service->validateUser($this->createStub(ServerRequestInterface::class));

        $this->assertTrue($security->hasValidSecurityContext());
        $this->assertSame('editor', $security->getLoginAggregate()->getLoginName());
        $this->assertFalse($security->isUserNameVerifiedWithinSession('editor'));
        $messages = $flash->getMessages();
        $this->assertCount(1, $messages);
        $this->assertSame(FlashSeverityEnum::WARNING, $messages[0]['severity']);
        $this->assertStringContainsString('Přihlášení zůstává', $messages[0]['message']);
    }

    public function testInvalidUserKeepsSessionAndMarksEventsLoginDeleted(): void
    {
        $security = $this->securityWithUser('editor');
        $flash = new Flash();
        $loginService = $this->createMock(LoginServiceInterface::class);
        $loginService->expects($this->never())->method('setAddUserNameToEventsLogin');
        $loginService->expects($this->once())->method('setDeleteUserNameFromEventsLogin')->with('editor');

        $service = $this->service($security, $flash, $loginService, new ValidatingException('Uživatel není validní.'));
        $service->validateUser($this->createStub(ServerRequestInterface::class));

        $this->assertTrue($security->hasValidSecurityContext());
        $this->assertSame('editor', $security->getLoginAggregate()->getLoginName());
        $this->assertSame([], $flash->getMessages());
    }

    public function testSuccessfulSyncAddsEventsLoginAndNotesTheAttempt(): void
    {
        $security = $this->securityWithUser('editor');
        $flash = new Flash();
        $loginService = $this->createMock(LoginServiceInterface::class);
        $loginService->expects($this->once())->method('setAddUserNameToEventsLogin')->with('editor');
        $loginService->expects($this->never())->method('setDeleteUserNameFromEventsLogin');

        $service = $this->service($security, $flash, $loginService, null);
        $service->validateUser($this->createStub(ServerRequestInterface::class));

        $this->assertTrue($security->hasValidSecurityContext());
        $this->assertTrue($security->isUserNameVerifiedWithinSession('editor'));
        $this->assertSame([], $flash->getMessages());
    }

    private function securityWithUser(string $loginName): Security
    {
        $login = new LoginAggregateFull();
        $login->setLoginName($loginName);
        $login->setPersisted();
        $security = new Security();
        $security->newContext($login);
        return $security;
    }

    private function service(Security $security, Flash $flash, LoginServiceInterface $loginService, ?\Throwable $failure): ValidatingService
    {
        $securityRepo = $this->createStub(StatusSecurityRepo::class);
        $securityRepo->method('get')->willReturn($security);
        $flashRepo = $this->createStub(StatusFlashRepo::class);
        $flashRepo->method('get')->willReturn($flash);
        $presentationRepo = $this->createStub(StatusPresentationRepo::class);

        return new class($securityRepo, $flashRepo, $presentationRepo, $loginService, $failure) extends ValidatingService {
            public function __construct(
                StatusSecurityRepo $statusSecurityRepo,
                StatusFlashRepo $statusFlashRepo,
                StatusPresentationRepo $statusPresentationRepo,
                LoginServiceInterface $loginService,
                private ?\Throwable $failure,
            ) {
                parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $loginService);
            }

            protected function validateUserByAuthServer(ServerRequestInterface $request, string $validatedUserName): void
            {
                if ($this->failure !== null) {
                    throw $this->failure;
                }
            }
        };
    }
}
