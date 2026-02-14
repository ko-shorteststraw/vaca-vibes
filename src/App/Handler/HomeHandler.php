<?php

declare(strict_types=1);

namespace App\Handler;

use App\Service\VacationRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomeHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $renderer,
        private VacationRepository $vacationRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $vacations = $this->vacationRepo->findAll();

        return new HtmlResponse($this->renderer->render('app::home', [
            'vacations' => $vacations,
        ]));
    }
}
