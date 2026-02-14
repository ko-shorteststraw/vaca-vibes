<?php

declare(strict_types=1);

namespace App\Handler\Itinerary;

use App\Service\ItineraryRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use starfederation\datastar\ServerSentEventGenerator;

class DeleteHandler implements RequestHandlerInterface
{
    public function __construct(
        private ItineraryRepository $itineraryRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $this->itineraryRepo->delete($id);

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();
        $sse->removeElements('#itinerary-item-' . $id);
        exit;
    }
}
