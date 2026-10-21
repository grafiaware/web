<?php
declare(strict_types=1);

namespace Test\Unit\Access;

use Access\AccessPresentation;
use Access\Enum\AccessPresentationEnum;
use Access\Enum\RoleEnum;
use Auth\Component\View\LoginComponent;
use Auth\Component\View\LogoutComponent;
use Component\View\ComponentInterface;
use Component\ViewModel\StatusViewModelInterface;
use PHPUnit\Framework\TestCase;
use Red\Component\View\Manage\ButtonsMenuComponent;

final class AccessPresentationTest extends TestCase
{
    public function testAnonymousCanDisplayLoginComponent(): void
    {
        $access = new AccessPresentation($this->statusViewModel(false, null));

        $this->assertTrue(
            $access->isAllowed(LoginComponent::class, AccessPresentationEnum::DISPLAY)
        );
    }

    public function testAnonymousCannotDisplayLogoutComponent(): void
    {
        $access = new AccessPresentation($this->statusViewModel(false, null));

        $this->assertFalse(
            $access->isAllowed(LogoutComponent::class, AccessPresentationEnum::DISPLAY)
        );
    }

    public function testAuthenticatedCanDisplayLogoutComponent(): void
    {
        $access = new AccessPresentation($this->statusViewModel(true, RoleEnum::VISITOR));

        $this->assertTrue(
            $access->isAllowed(LogoutComponent::class, AccessPresentationEnum::DISPLAY)
        );
    }

    public function testEditorCanEditButtonsMenuComponent(): void
    {
        $access = new AccessPresentation($this->statusViewModel(true, RoleEnum::EDITOR));

        $this->assertTrue(
            $access->isAllowed(ButtonsMenuComponent::class, AccessPresentationEnum::EDIT)
        );
    }

    public function testVisitorCannotEditButtonsMenuComponent(): void
    {
        $access = new AccessPresentation($this->statusViewModel(true, RoleEnum::VISITOR));

        $this->assertFalse(
            $access->isAllowed(ButtonsMenuComponent::class, AccessPresentationEnum::EDIT)
        );
    }

    public function testHasAnyPermissionUsesRoleLevelPermission(): void
    {
        $access = new AccessPresentation($this->statusViewModel(true, RoleEnum::SUPERVISOR));

        $this->assertTrue($access->hasAnyPermission(ButtonsMenuComponent::class));
    }

    public function testClosurePermissionIsEvaluated(): void
    {
        $access = new AccessPresentation($this->statusViewModel(true, RoleEnum::EDITOR));

        $this->assertTrue(
            $access->isAllowed(PermissiveClosureComponent::class, AccessPresentationEnum::DISPLAY)
        );
    }

    private function statusViewModel(bool $logged, ?string $role): StatusViewModelInterface
    {
        $mock = $this->createMock(StatusViewModelInterface::class);
        $mock->method('isUserLoggedIn')->willReturn($logged);
        $mock->method('getUserRole')->willReturn($role);

        return $mock;
    }
}

final class PermissiveClosureComponent implements ComponentInterface
{
    public static function getComponentPermissions(): array
    {
        return [
            RoleEnum::EDITOR => [
                AccessPresentationEnum::DISPLAY => static fn(): bool => true,
            ],
        ];
    }
}
