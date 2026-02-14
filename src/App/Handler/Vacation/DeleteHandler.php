<?php

declare(strict_types=1);

namespace App\Handler\Vacation;

use App\Service\VacationRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use starfederation\datastar\ServerSentEventGenerator;

class DeleteHandler implements RequestHandlerInterface
{
    public function __construct(
        private VacationRepository $vacationRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $this->vacationRepo->delete($id);

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();
        $sse->location('/');
        exit;
    }
}
