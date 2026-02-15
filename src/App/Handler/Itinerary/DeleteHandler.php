<?php

declare(strict_types=1);

namespace App\Handler\Itinerary;

use App\Service\ItineraryRepository;
use App\Service\VacationRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use starfederation\datastar\ServerSentEventGenerator;

class DeleteHandler implements RequestHandlerInterface
{
    public function __construct(
        private ItineraryRepository $itineraryRepo,
        private VacationRepository $vacationRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');
        $id = (int) $request->getAttribute('id');

        $item = $this->itineraryRepo->findById($id);
        if ($item) {
            $vacation = $this->vacationRepo->findById((int) $item['vacation_id']);
            if (! $vacation || (int) $vacation['user_id'] !== $user['id']) {
                $sse = new ServerSentEventGenerator();
                $sse->sendHeaders();
                exit;
            }
        }

        $this->itineraryRepo->delete($id);

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();
        $sse->removeElements('#itinerary-item-' . $id);
        exit;
    }
}
