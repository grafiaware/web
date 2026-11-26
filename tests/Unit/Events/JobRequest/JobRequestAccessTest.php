<?php
declare(strict_types=1);

namespace Test\Unit\Events\JobRequest;

use Access\Enum\RoleEnum;
use Component\ViewModel\StatusViewModelInterface;
use Events\Component\ViewModel\Data\JobRequestAccessTrait;
use Events\Model\Entity\JobInterface;
use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\RepresentativeRepoInterface;
use PHPUnit\Framework\TestCase;

/**
 * #171: zájmy o pozici (jméno, e-mail, telefon, CV) vidí jen zástupce firmy pozice, administrátor a autor zájmu.
 */
final class JobRequestAccessTest extends TestCase {

    private const JOB = 10;
    private const COMPANY = 3;

    private function access(?string $role, ?string $login): object {
        $status = $this->createMock(StatusViewModelInterface::class);
        $status->method('getUserRole')->willReturn($role);
        $status->method('getUserLoginName')->willReturn($login);
        $job = $this->createMock(JobInterface::class);
        $job->method('getCompanyId')->willReturn(self::COMPANY);
        $jobRepo = $this->createMock(JobRepoInterface::class);
        $jobRepo->method('get')->willReturnCallback(fn($id) => (int) $id === self::JOB ? $job : null);
        $representativeRepo = $this->createMock(RepresentativeRepoInterface::class);
        $representative = $this->createMock(RepresentativeInterface::class);
        $representativeRepo->method('get')->willReturnCallback(
            fn($l, $c) => ($l === 'zastupce' && (int) $c === self::COMPANY) ? $representative : null);

        return new class($status, $jobRepo, $representativeRepo) {
            use JobRequestAccessTrait;
            public function __construct(private $status, private $jobRepo, private $representativeRepo) {
            }
            public function all($jobId): bool {
                return $this->canSeeAllJobRequests($jobId);
            }
            public function one($jobId, $login): bool {
                return $this->canSeeJobRequest($jobId, $login);
            }
        };
    }

    public function testAnonymousSeesNothing(): void {
        $a = $this->access(null, null);
        $this->assertFalse($a->all(self::JOB));
        $this->assertFalse($a->one(self::JOB, 'navstevnik'));
    }

    public function testRepresentativeOfJobCompanySeesAll(): void {
        $a = $this->access(RoleEnum::REPRESENTATIVE, 'zastupce');
        $this->assertTrue($a->all(self::JOB));
        $this->assertTrue($a->one(self::JOB, 'navstevnik'));
        $this->assertFalse($a->all(999), 'neexistující pozice');
    }

    public function testRepresentativeOfOtherCompanySeesNothing(): void {
        $a = $this->access(RoleEnum::REPRESENTATIVE, 'jiny-zastupce');
        $this->assertFalse($a->all(self::JOB));
        $this->assertFalse($a->one(self::JOB, 'navstevnik'));
    }

    public function testAdministratorSeesAll(): void {
        $this->assertTrue($this->access(RoleEnum::EVENTS_ADMINISTRATOR, 'admin')->all(self::JOB));
    }

    public function testVisitorSeesOnlyOwn(): void {
        $a = $this->access(RoleEnum::VISITOR, 'navstevnik');
        $this->assertFalse($a->all(self::JOB));
        $this->assertTrue($a->one(self::JOB, 'navstevnik'));
        $this->assertFalse($a->one(self::JOB, 'jiny-navstevnik'));
    }

    public function testOtherLoggedInRoleSeesNothing(): void {
        $a = $this->access('editor', 'navstevnik');
        $this->assertFalse($a->one(self::JOB, 'navstevnik'), 'stejné jméno, ale ne role návštěvník');
    }
}
