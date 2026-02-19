<?php

declare(strict_types=1);

namespace App\Handler\Itinerary;

use App\Service\ExpenseRepository;
use App\Service\ItineraryRepository;
use App\Service\VacationRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use starfederation\datastar\enums\ElementPatchMode;
use starfederation\datastar\ServerSentEventGenerator;

class AddSuggestedHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $renderer,
        private ItineraryRepository $itineraryRepo,
        private VacationRepository $vacationRepo,
        private ExpenseRepository $expenseRepo,
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
            'day_number'   => 1,
            'title'        => $signals['suggestedTitle'] ?? '',
            'description'  => $signals['suggestedDescription'] ?? null,
            'time'         => null,
            'cost'         => (float) ($signals['suggestedCost'] ?? 0),
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
        $total = $this->itineraryRepo->sumCostByVacation($vacationId);
        $sse->patchElements(
            '<span id="itinerary-cost-total" class="ml-3 tag is-info is-light">$' . number_format($total, 2) . '</span>',
        );
        $budgetHtml = $this->renderer->render('partial::budget-summary', [
            'vacation'           => $vacation,
            'totalExpenses'      => $this->expenseRepo->sumByVacation($vacationId),
            'itineraryCostTotal' => $total,
        ]);
        $sse->patchElements($budgetHtml, ['selector' => '#budget-summary']);
        exit;
    }
}
