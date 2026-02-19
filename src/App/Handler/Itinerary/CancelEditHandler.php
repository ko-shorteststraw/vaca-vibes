<?php

declare(strict_types=1);

namespace App\Handler\Itinerary;

use App\Service\ItineraryRepository;
use App\Service\VacationRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use starfederation\datastar\ServerSentEventGenerator;

class CancelEditHandler implements RequestHandlerInterface
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
        $id = (int) $request->getAttribute('id');

        $item = $this->itineraryRepo->findById($id);
        if (! $item) {
            $sse = new ServerSentEventGenerator();
            $sse->sendHeaders();
            exit;
        }

        $vacation = $this->vacationRepo->findById((int) $item['vacation_id']);
        if (! $vacation || (int) $vacation['user_id'] !== $user['id']) {
            $sse = new ServerSentEventGenerator();
            $sse->sendHeaders();
            exit;
        }

        $html = $this->renderer->render('partial::itinerary-item', ['item' => $item]);

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();
        $sse->patchElements($html, [
            'selector' => '#itinerary-edit-' . $id,
        ]);
        exit;
    }
}
