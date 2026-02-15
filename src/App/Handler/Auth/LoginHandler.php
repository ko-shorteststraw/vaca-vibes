<?php

declare(strict_types=1);

namespace App\Handler\Auth;

use App\Service\UserRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoginHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $renderer,
        private UserRepository $userRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getAttribute('user')) {
            return new RedirectResponse('/');
        }

        if ($request->getMethod() === 'POST') {
            return $this->handlePost($request);
        }

        return new HtmlResponse($this->renderer->render('app::login'));
    }

    private function handlePost(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        $username = trim($body['username'] ?? '');
        $password = $body['password'] ?? '';

        $user = $this->userRepo->findByUsername($username);

        if (! $user || ! password_verify($password, $user['password'])) {
            return new HtmlResponse($this->renderer->render('app::login', [
                'error' => 'Invalid username or password.',
                'username' => $username,
            ]));
        }

        $_SESSION['user_id'] = $user['id'];

        return new RedirectResponse('/');
    }
}
