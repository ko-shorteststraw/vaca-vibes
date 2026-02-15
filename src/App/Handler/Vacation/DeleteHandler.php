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
        $user = $request->getAttribute('user');
        $id = (int) $request->getAttribute('id');

        $vacation = $this->vacationRepo->findById($id);
        if (! $vacation || (int) $vacation['user_id'] !== $user['id']) {
            $sse = new ServerSentEventGenerator();
            $sse->sendHeaders();
            $sse->location('/');
            exit;
        }

        $this->vacationRepo->delete($id);

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();
        $sse->location('/');
        exit;
    }
}
