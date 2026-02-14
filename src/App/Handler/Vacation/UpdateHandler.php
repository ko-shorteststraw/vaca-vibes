<?php

declare(strict_types=1);

namespace App\Handler\Vacation;

use App\Service\VacationRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use starfederation\datastar\ServerSentEventGenerator;

class UpdateHandler implements RequestHandlerInterface
{
    public function __construct(
        private VacationRepository $vacationRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $signals = ServerSentEventGenerator::readSignals();

        $this->vacationRepo->update($id, [
            'destination' => $signals['destination'] ?? '',
            'start_date'  => $signals['startDate'] ?? null,
            'end_date'    => $signals['endDate'] ?? null,
            'budget'      => $signals['budget'] ?? 0,
            'notes'       => $signals['notes'] ?? null,
            'image_url'   => $signals['imageUrl'] ?? null,
        ]);

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();
        $sse->location('/vacation/' . $id);
        exit;
    }
}
