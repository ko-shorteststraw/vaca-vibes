<?php

declare(strict_types=1);

namespace App\Handler\Admin;

use App\Service\UserRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UsersHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $renderer,
        private UserRepository $userRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');
        $users = $this->userRepo->findAll();

        return new HtmlResponse($this->renderer->render('app::admin-users', [
            'user' => $user,
            'users' => $users,
        ]));
    }
}
