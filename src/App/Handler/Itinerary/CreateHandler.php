<?php

declare(strict_types=1);

namespace App\Handler\Itinerary;

use App\Service\ItineraryRepository;
use App\Service\VacationRepository;
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
        private VacationRepository $vacationRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');
        $vacationId = (int) $request->getAttribute('id');

        $vacation = $this->vacationRepo->findById($vacationId);
        if (! $vacation || (int) $vacation['user_id'] !== $user['id']) {
            $sse = new ServerSentEventGenerator();
            $sse->sendHeaders();
            exit;
        }

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
