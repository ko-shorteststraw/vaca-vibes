<?php

declare(strict_types=1);

namespace App\Handler\Admin;

use App\Service\UserRepository;
use App\Service\VacationRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DashboardHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $renderer,
        private UserRepository $userRepo,
        private VacationRepository $vacationRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');

        return new HtmlResponse($this->renderer->render('app::admin-dashboard', [
            'user' => $user,
            'totalUsers' => $this->userRepo->countAll(),
            'totalVacations' => $this->vacationRepo->countAll(),
        ]));
    }
}
