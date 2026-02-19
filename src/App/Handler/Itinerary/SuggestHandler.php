<?php

declare(strict_types=1);

namespace App\Handler\Itinerary;

use App\Service\SuggestionProvider;
use App\Service\VacationRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use starfederation\datastar\ServerSentEventGenerator;

class SuggestHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $renderer,
        private VacationRepository $vacationRepo,
        private SuggestionProvider $suggestionProvider,
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

        $activities = $this->suggestionProvider->findActivitiesByDestination($vacation['destination']);

        $html = $this->renderer->render('partial::suggested-activities', [
            'activities' => $activities,
            'vacationId' => $vacationId,
            'destination' => $vacation['destination'],
        ]);

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();
        $sse->patchElements($html, [
            'selector' => '#suggested-activities-container',
            'mode' => \starfederation\datastar\enums\ElementPatchMode::Inner,
        ]);
        exit;
    }
}
