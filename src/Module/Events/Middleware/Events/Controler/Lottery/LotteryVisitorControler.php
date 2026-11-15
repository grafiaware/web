<?php

namespace Events\Middleware\Events\Controler\Lottery;

use Auth\Service\Qr\QrImageGenerator;
use Events\Service\Lottery\AuthLookup;
use Events\Service\Lottery\LotteryMailer;
use Events\Service\Lottery\LotteryService;
use Events\Service\Lottery\VisitorCodeGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * D1 (#46): návštěvník – zapojení do losování s anketou, osobní QR kód (stránka „Můj kód“ offline, obrázek),
 * adresa skenu fotoaparátem. Zadání: docs/vp/zadani/D1.md.
 *
 * POST /events/v1/lottery/:id/join     zapojení (souhlas + anketa), e-mail s QR
 * GET  /events/v1/mycode/page          stránka Můj kód (service worker ji uloží pro offline)
 * GET  /events/v1/mycode/image         PNG s QR kódem ke stažení
 * GET  /events/v1/mycode/sw            service worker stránky Můj kód
 * GET  /events/v1/scan/:code           QR načtený fotoaparátem: obsluha → návštěvník a tlačítka, ostatní → „Ukažte kód obsluze“
 */
class LotteryVisitorControler extends LotteryControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LotteryService $lotteries,
            private QrImageGenerator $qr,
            private AuthLookup $authLookup,
            private LotteryMailer $mailer) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $lotteries);
    }

    public function join(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isVisitor()) {
            $this->addFlashMessage('Do losování se mohou zapojit jen přihlášení návštěvníci.', FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        $login = $this->loginName();
        $lotteryId = (int) $id;
        $wasJoined = $this->lotteries->isJoined($lotteryId, $login);
        $answers = $this->param($request, 'q', []);
        $errors = $this->lotteries->join($lotteryId, $login, (bool) $this->param($request, 'consent', false), is_array($answers) ? $answers : []);
        if ($errors) {
            foreach ($errors as $error) {
                $this->addFlashMessage($error, FlashSeverityEnum::WARNING);
            }
            return $this->redirectSeeLastGet($request);
        }
        if ($wasJoined) {
            $this->addFlashMessage('Odpovědi ankety jsme uložili.', FlashSeverityEnum::SUCCESS);
            return $this->redirectSeeLastGet($request);
        }
        $this->sendJoinedMail($request, $lotteryId, $login);
        $this->addFlashMessage('Jste zapojeni do losování. Na veletrhu ukažte u vchodu svůj QR kód – uložte si ho do telefonu.', FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request);
    }

    public function myCodePage(ServerRequestInterface $request): ResponseInterface {
        if (!$this->isVisitor()) {
            return $this->page($request, 'Můj kód', 'text.php',
                ['text' => 'Svůj QR kód pro losování uvidíte po přihlášení jako návštěvník.'], 403);
        }
        $login = $this->loginName();
        $code = $this->lotteries->codeFor($login);
        $png = $this->qr->generate($this->scanUrl($request, $code), 'Veletrh práce – losování')->getString();
        $joined = array_values(array_filter($this->lotteries->openLotteries(), fn($l) => $this->lotteries->isJoined((int) $l['id'], $login)));
        return $this->page($request, 'Můj kód', 'mycode.php', [
            'name' => $this->lotteries->displayName($login),
            'code' => $code,
            'qrDataUri' => 'data:image/png;base64,' . base64_encode($png),
            'joined' => $joined,
            'swUrl' => 'events/v1/mycode/sw',
        ])->withHeader('Cache-Control', 'private, no-cache');
    }

    public function myCodeImage(ServerRequestInterface $request): ResponseInterface {
        if (!$this->isVisitor()) {
            return $this->page($request, 'Můj kód', 'text.php', ['text' => 'Přihlaste se jako návštěvník.'], 403);
        }
        $code = $this->lotteries->codeFor($this->loginName());
        $png = $this->qr->generate($this->scanUrl($request, $code), 'Veletrh práce – losování')->getString();
        return $this->createStringOKResponse($png)
            ->withHeader('Content-Type', 'image/png')
            ->withHeader('Content-Disposition', 'attachment; filename="veletrh-qr-kod.png"')
            ->withHeader('Cache-Control', 'private, max-age=86400');
    }

    /**
     * Service worker: stránku Můj kód načte ze sítě, a když síť nejde, vrátí poslední uloženou verzi.
     */
    public function serviceWorker(ServerRequestInterface $request): ResponseInterface {
        $js = <<<'JS'
// D1: offline stránka „Můj kód“ (QR kód pro losování). Síť první, při výpadku uložená verze.
const CACHE = 'vp-mycode-v1';
self.addEventListener('install', (e) => { self.skipWaiting(); });
self.addEventListener('activate', (e) => { e.waitUntil(self.clients.claim()); });
self.addEventListener('fetch', (e) => {
    const req = e.request;
    if (req.method !== 'GET') { return; }
    e.respondWith(fetch(req).then((res) => {
        if (res.ok) { const copy = res.clone(); caches.open(CACHE).then((c) => c.put(req, copy)); }
        return res;
    }).catch(() => caches.match(req).then((cached) => cached || new Response(
        '<!DOCTYPE html><meta charset="utf-8"><meta name="viewport" content="width=device-width"><p style="font:18px Arial;padding:16px">Stránka zatím není uložená. Otevřete ji jednou s připojením k internetu.</p>',
        {headers: {'Content-Type': 'text/html; charset=utf-8'}}))));
});
JS;
        return $this->createStringOKResponse($js)
            ->withHeader('Content-Type', 'application/javascript; charset=utf-8')
            ->withHeader('Cache-Control', 'no-cache');
    }

    public function scanLanding(ServerRequestInterface $request, $code): ResponseInterface {
        $code = VisitorCodeGenerator::fromScannedText((string) $code);
        if (!$this->isStaff()) {
            return $this->page($request, 'Kód pro losování', 'text.php',
                ['text' => 'Toto je váš kód pro losování. Ukažte ho obsluze u vchodu nebo na infostánku.']);
        }
        $login = $code !== null ? $this->lotteries->loginByCode($code) : null;
        if ($login === null) {
            return $this->page($request, 'Neznámý kód', 'text.php', ['text' => 'Neznámý nebo neplatný kód.'], 404);
        }
        $items = [];
        foreach ($this->lotteries->openLotteries() as $l) {
            $items[] = ['lottery' => $l, 'status' => $this->lotteries->visitorStatus((int) $l['id'], $login)];
        }
        return $this->page($request, 'Návštěvník', 'scan-landing.php', [
            'login' => $login,
            'name' => $this->lotteries->displayName($login),
            'items' => $items,
            'returnUrl' => 'events/v1/scan/' . $code,
        ]);
    }

    private function scanUrl(ServerRequestInterface $request, string $code): string {
        return $this->absoluteUrl($request, 'events/v1/scan/' . $code);
    }

    private function sendJoinedMail(ServerRequestInterface $request, int $lotteryId, string $login): void {
        try {
            $email = $this->authLookup->emailOf($login);
            if ($email === null) {
                return;
            }
            $code = $this->lotteries->codeFor($login);
            $png = $this->qr->generate($this->scanUrl($request, $code), 'Veletrh práce – losování')->getString();
            $lottery = $this->lotteries->lottery($lotteryId);
            $this->mailer->sendJoined($email, $this->lotteries->displayName($login), (string) ($lottery['name'] ?? ''), $png,
                $this->absoluteUrl($request, 'events/v1/mycode/page'));
        } catch (\Throwable $e) {
            // zapojení platí i bez e-mailu (QR je v profilu); chyba odeslání se jen zaloguje
            error_log('D1 lottery joined mail failed: ' . $e->getMessage());
        }
    }
}
