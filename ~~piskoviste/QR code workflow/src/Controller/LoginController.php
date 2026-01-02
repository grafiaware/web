<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */
namespace App\Controller;

use App\Domain\Auth\Service\QrLoginService;
use App\Domain\Auth\Repository\UserRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;

/**
 * Description of LoginController
 *
 * @author pes2704
 */
final class LoginController
{
    public function __construct(
        UserRepositoryInterface $users,     // private UserRepositoryInterface $users,
        QrLoginService $qrLogin     // private QrLoginService $qrLogin
    ) {}

    /**
     * Zobrazí login stránku.
     */
    public function loginPage(ServerRequestInterface $request): ResponseInterface
    {
        // Vygenerujeme QR token
        $token = $this->qrLogin->generateToken();

        return new HtmlResponse(
            $this->render('login.twig', [
                'qrToken' => $token,
                'qrUrl'   => '/qr-auth/' . $token,
            ])
        );
    }

    /**
     * API endpoint, který čte stav QR loginu.
     *
     * Polluje se z JS každou 1s.
     */
    public function qrStatus(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $token = $args['token'];
        $data = $this->qrLogin->validateToken($token);

        if (!$data) {
            return new JsonResponse(['status' => 'expired']);
        }

        if ($data['user_id']) {
            // Login byl potvrzen
            $this->qrLogin->consume($token);

            // Vytvoříme session
            $_SESSION['user_id'] = $data['user_id'];

            return new JsonResponse(['status' => 'authenticated']);
        }

        return new JsonResponse(['status' => 'pending']);
    }


    /**
     * Mobilní stránka, která umožní dokončit login po načtení QR kódu.
     */
    public function qrAuth(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $token = $args['token'];

        // Uživatel se musí nyní přihlásit (OAuth nebo heslem)
        return new HtmlResponse(
            $this->render('qr-auth.twig', [
                'token' => $token,
            ])
        );
    }

    /**
     * Dokončení loginu po úspěšném OAuth / hesle.
     */
    public function confirmQrLogin(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $token = $data['token'];
        $userId = $_SESSION['user_id'] ?? null;

        if (!$token || !$userId) {
            return new RedirectResponse('/login');
        }

        $this->qrLogin->assignUser($token, $userId);

        return new HtmlResponse("Zařízení je úspěšně autentizováno. Můžete se vrátit zpět.");
    }


    private function render(string $template, array $data): string
    {
        // jednouchá varianta – v reálném projektu zde bývá Twig/Latte
        extract($data);
        ob_start();
        include __DIR__ . '/../../templates/' . $template;
        return ob_get_clean();
    }
}
