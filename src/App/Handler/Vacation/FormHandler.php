<?php

declare(strict_types=1);

namespace App\Handler\Vacation;

use App\Service\SuggestionProvider;
use App\Service\VacationRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use starfederation\datastar\ServerSentEventGenerator;

class FormHandler implements RequestHandlerInterface
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
        $id = $request->getAttribute('id');
        $isEdit = $id !== null;
        $vacation = $isEdit ? $this->vacationRepo->findById((int) $id) : [];

        if (! $isEdit) {
            $suggestionId = $request->getQueryParams()['suggestion'] ?? null;
            if ($suggestionId !== null) {
                $suggestion = $this->suggestionProvider->findById((int) $suggestionId);
                if ($suggestion) {
                    $vacation = [
                        'destination' => $suggestion['destination'],
                        'budget'      => $suggestion['budget'],
                        'notes'       => $suggestion['notes'],
                        'image_url'   => $suggestion['image_url'],
                        'start_date'  => $suggestion['start_date'],
                        'end_date'    => $suggestion['end_date'],
                    ];
                }
            }
        }

        if ($isEdit && (! $vacation || (int) $vacation['user_id'] !== $user['id'])) {
            $sse = new ServerSentEventGenerator();
            $sse->sendHeaders();
            exit;
        }

        $targetSelector = $isEdit ? '#vacation-edit-container' : '#vacation-form-container';

        $html = $this->renderer->render('partial::vacation-form', [
            'vacation' => $vacation ?: [],
            'isEdit'   => $isEdit,
        ]);

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();
        $sse->patchElements($html, ['selector' => $targetSelector]);
        exit;
    }
}
