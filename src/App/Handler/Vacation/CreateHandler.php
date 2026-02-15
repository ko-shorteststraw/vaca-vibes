<?php

declare(strict_types=1);

namespace App\Handler\Vacation;

use App\Service\VacationRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use starfederation\datastar\ServerSentEventGenerator;

class CreateHandler implements RequestHandlerInterface
{
    public function __construct(
        private VacationRepository $vacationRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');
        $signals = ServerSentEventGenerator::readSignals();

        $this->vacationRepo->create([
            'destination' => $signals['destination'] ?? '',
            'start_date'  => $signals['startDate'] ?? null,
            'end_date'    => $signals['endDate'] ?? null,
            'budget'      => $signals['budget'] ?? 0,
            'notes'       => $signals['notes'] ?? null,
            'image_url'   => $signals['imageUrl'] ?? null,
            'user_id'     => $user['id'],
        ]);

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();
        $sse->location('/');
        exit;
    }
}
