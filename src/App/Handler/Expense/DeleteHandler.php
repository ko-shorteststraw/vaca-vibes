<?php

declare(strict_types=1);

namespace App\Handler\Expense;

use App\Service\ExpenseRepository;
use App\Service\VacationRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use starfederation\datastar\ServerSentEventGenerator;

class DeleteHandler implements RequestHandlerInterface
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
        $id = (int) $request->getAttribute('id');
        $expense = $this->expenseRepo->findById($id);

        if ($expense) {
            $vacation = $this->vacationRepo->findById((int) $expense['vacation_id']);
            if (! $vacation || (int) $vacation['user_id'] !== $user['id']) {
                $sse = new ServerSentEventGenerator();
                $sse->sendHeaders();
                exit;
            }
        }

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();

        if ($expense) {
            $vacationId = (int) $expense['vacation_id'];
            $this->expenseRepo->delete($id);

            $vacation = $this->vacationRepo->findById($vacationId);
            $totalExpenses = $this->expenseRepo->sumByVacation($vacationId);

            $budgetHtml = $this->renderer->render('partial::budget-summary', [
                'vacation'      => $vacation,
                'totalExpenses' => $totalExpenses,
            ]);

            $sse->removeElements('#expense-item-' . $id);
            $sse->patchElements($budgetHtml, ['selector' => '#budget-summary']);
        } else {
            $sse->removeElements('#expense-item-' . $id);
        }

        exit;
    }
}
