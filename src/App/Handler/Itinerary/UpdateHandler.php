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
use starfederation\datastar\ServerSentEventGenerator;

class UpdateHandler implements RequestHandlerInterface
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

        $signals = ServerSentEventGenerator::readSignals();

        $this->itineraryRepo->update($id, [
            'day_number'  => (int) ($signals['editDay'] ?? $item['day_number']),
            'title'       => $signals['editTitle'] ?? $item['title'],
            'description' => $signals['editDescription'] ?? $item['description'],
            'time'        => $signals['editTime'] ?? $item['time'],
            'cost'        => (float) ($signals['editCost'] ?? $item['cost']),
        ]);

        $updatedItem = $this->itineraryRepo->findById($id);
        $html = $this->renderer->render('partial::itinerary-item', ['item' => $updatedItem]);

        $vacationId = (int) $item['vacation_id'];
        $total = $this->itineraryRepo->sumCostByVacation($vacationId);

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();
        $sse->patchElements($html, [
            'selector' => '#itinerary-edit-' . $id,
        ]);
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
