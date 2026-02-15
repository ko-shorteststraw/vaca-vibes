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

class RegisterHandler implements RequestHandlerInterface
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

        return new HtmlResponse($this->renderer->render('app::register'));
    }

    private function handlePost(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        $username = trim($body['username'] ?? '');
        $password = $body['password'] ?? '';
        $passwordConfirm = $body['password_confirm'] ?? '';

        $errors = [];

        if (strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters.';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }
        if (! $errors && $this->userRepo->findByUsername($username)) {
            $errors[] = 'Username is already taken.';
        }

        if ($errors) {
            return new HtmlResponse($this->renderer->render('app::register', [
                'errors' => $errors,
                'username' => $username,
            ]));
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $userId = $this->userRepo->create($username, $hash);

        $_SESSION['user_id'] = $userId;

        return new RedirectResponse('/');
    }
}
