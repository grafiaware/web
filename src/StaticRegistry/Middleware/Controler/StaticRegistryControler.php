<?php

namespace StaticRegistry\Middleware\Controler;

use FrontControler\FrontControlerAbstract;
use Pes\Http\Response\StatusEnum;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use StaticRegistry\Model\Entity\StaticRegistryEntry;
use StaticRegistry\Model\Repository\StaticRegistryRepoInterface;
use StaticRegistry\Service\StaticRegistryTokenValidator;
use StaticRegistry\Service\StaticTemplateScannerInterface;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use UnexpectedValueException;

class StaticRegistryControler extends FrontControlerAbstract {

    /**
     * @param array<string, mixed> $registryConfig
     */
    public function __construct(
        StatusSecurityRepo $statusSecurityRepo,
        StatusFlashRepo $statusFlashRepo,
        StatusPresentationRepo $statusPresentationRepo,
        private StaticRegistryRepoInterface $staticRegistryRepo,
        private StaticTemplateScannerInterface $templateScanner,
        private StaticRegistryTokenValidator $tokenValidator,
        private array $registryConfig,
    ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
    }

    public function upsert(ServerRequestInterface $request, int $menuItemId): ResponseInterface {
        if (!$this->assertToken($request)) {
            return $this->createJsonOKResponse(['error' => 'invalid_token'], StatusEnum::_401_Unauthorized);
        }

        /** @var array<string, mixed> $payload */
        $payload = [];
        $path = '';
        try {
            $payload = $this->decodeJsonBody($request);
            $path = (string) ($payload['path'] ?? '');
            $this->assertPathPrefix($path);
        } catch (UnexpectedValueException $e) {
            return $this->createJsonOKResponse(['error' => $e->getMessage()], StatusEnum::_400_BadRequest);
        }
        $entry = (new StaticRegistryEntry())
            ->setMenuItemId($menuItemId)
            ->setRedStaticId((int) ($payload['redStaticId'] ?? 0))
            ->setPath($path)
            ->setTemplate((string) ($payload['template'] ?? ''))
            ->setCreator(isset($payload['creator']) ? (string) $payload['creator'] : null)
            ->setUpdated((string) ($payload['updated'] ?? date(DATE_ATOM)))
            ->setSiteCode((string) ($payload['siteCode'] ?? $this->registryConfig['siteCode']));

        $upserted = $this->staticRegistryRepo->upsert($entry);
        return $this->createJsonOKResponse([
            'menuItemId' => $menuItemId,
            'status' => $upserted ? 'upserted' : 'skipped',
            'reason' => $upserted ? null : 'older_than_local',
        ]);
    }

    public function delete(ServerRequestInterface $request, int $menuItemId): ResponseInterface {
        if (!$this->assertToken($request)) {
            return $this->createJsonOKResponse(['error' => 'invalid_token'], StatusEnum::_401_Unauthorized);
        }
        $this->staticRegistryRepo->delete($menuItemId);
        return $this->createPutNoContentResponse();
    }

    public function get(ServerRequestInterface $request, int $menuItemId): ResponseInterface {
        if (!$this->assertToken($request)) {
            return $this->createJsonOKResponse(['error' => 'invalid_token'], StatusEnum::_401_Unauthorized);
        }
        $entry = $this->staticRegistryRepo->getByMenuItemId($menuItemId);
        if ($entry === null) {
            return $this->createJsonOKResponse(['error' => 'not_found'], StatusEnum::_404_NotFound);
        }
        return $this->createJsonOKResponse([
            'menuItemId' => $entry->getMenuItemId(),
            'redStaticId' => $entry->getRedStaticId(),
            'path' => $entry->getPath(),
            'template' => $entry->getTemplate(),
            'creator' => $entry->getCreator(),
            'updated' => $entry->getUpdated(),
            'siteCode' => $entry->getSiteCode(),
        ]);
    }

    public function templates(ServerRequestInterface $request): ResponseInterface {
        if (!$this->assertToken($request)) {
            return $this->createJsonOKResponse(['error' => 'invalid_token'], StatusEnum::_401_Unauthorized);
        }
        $queryParams = $request->getQueryParams();
        $prefix = (string) ($queryParams['prefix'] ?? $this->registryConfig['pathPrefix']);
        $siteCode = (string) ($queryParams['siteCode'] ?? $this->registryConfig['siteCode']);
        return $this->createJsonOKResponse($this->templateScanner->scan($prefix, $siteCode));
    }

    private function assertToken(ServerRequestInterface $request): bool {
        return $this->tokenValidator->isValid($request, (string) $this->registryConfig['token']);
    }

    private function assertPathPrefix(string $path): void {
        if ($path === '') {
            return;
        }
        $expectedPrefix = (string) $this->registryConfig['pathPrefix'];
        if (!str_starts_with($path, $expectedPrefix)) {
            throw new UnexpectedValueException("invalid_path_prefix");
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonBody(ServerRequestInterface $request): array {
        $raw = (string) $request->getBody();
        if ($raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new UnexpectedValueException('invalid_json');
        }
        return $decoded;
    }
}
