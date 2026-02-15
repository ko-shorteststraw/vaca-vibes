<?php

declare(strict_types=1);

namespace App\Handler\Expense;

use App\Service\ExpenseRepository;
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
        private ExpenseRepository $expenseRepo,
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

        $expenseId = $this->expenseRepo->create([
            'vacation_id' => $vacationId,
            'description' => $signals['expDescription'] ?? '',
            'amount'      => (float) ($signals['expAmount'] ?? 0),
            'category'    => $signals['expCategory'] ?? null,
        ]);

        $expense = $this->expenseRepo->findById($expenseId);
        $vacation = $this->vacationRepo->findById($vacationId);
        $totalExpenses = $this->expenseRepo->sumByVacation($vacationId);

        $expenseHtml = $this->renderer->render('partial::expense-item', ['expense' => $expense]);
        $budgetHtml = $this->renderer->render('partial::budget-summary', [
            'vacation'      => $vacation,
            'totalExpenses' => $totalExpenses,
        ]);

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();
        $sse->removeElements('#expense-empty');
        $sse->patchElements($expenseHtml, [
            'selector' => '#expense-list',
            'mode' => ElementPatchMode::Append,
        ]);
        $sse->patchElements($budgetHtml, ['selector' => '#budget-summary']);
        $sse->patchSignals([
            'expDescription' => '',
            'expAmount' => '0',
            'expCategory' => '',
        ]);
        exit;
    }
}
