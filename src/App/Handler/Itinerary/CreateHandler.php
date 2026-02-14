<?php

declare(strict_types=1);

namespace App\Handler\Itinerary;

use App\Service\ItineraryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use starfederation\datastar\enums\ElementPatchMode;
use starfederation\datastar\ServerSentEventGenerator;

class CreateHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $renderer,
        private ItineraryRepository $itineraryRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $vacationId = (int) $request->getAttribute('id');
        $signals = ServerSentEventGenerator::readSignals();

        $itemId = $this->itineraryRepo->create([
            'vacation_id'  => $vacationId,
            'day_number'   => (int) ($signals['itinDay'] ?? 1),
            'title'        => $signals['itinTitle'] ?? '',
            'description'  => $signals['itinDescription'] ?? null,
            'time'         => $signals['itinTime'] ?? null,
            'cost'         => (float) ($signals['itinCost'] ?? 0),
        ]);

        $item = $this->itineraryRepo->findById($itemId);
        $html = $this->renderer->render('partial::itinerary-item', ['item' => $item]);

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();
        $sse->removeElements('#itinerary-empty');
        $sse->patchElements($html, [
            'selector' => '#itinerary-list',
            'mode' => ElementPatchMode::Append,
        ]);
        $sse->patchSignals([
            'itinTitle' => '',
            'itinDescription' => '',
            'itinTime' => '',
            'itinCost' => '0',
        ]);
        exit;
    }
}
