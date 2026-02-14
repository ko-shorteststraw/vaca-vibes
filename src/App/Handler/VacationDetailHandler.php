<?php

declare(strict_types=1);

namespace App\Handler;

use App\Service\ExpenseRepository;
use App\Service\ItineraryRepository;
use App\Service\VacationRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class VacationDetailHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $renderer,
        private VacationRepository $vacationRepo,
        private ItineraryRepository $itineraryRepo,
        private ExpenseRepository $expenseRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $vacation = $this->vacationRepo->findById($id);

        if (! $vacation) {
            return new HtmlResponse('Vacation not found', 404);
        }

        $itineraryItems = $this->itineraryRepo->findByVacation($id);
        $expenses = $this->expenseRepo->findByVacation($id);
        $totalExpenses = $this->expenseRepo->sumByVacation($id);

        return new HtmlResponse($this->renderer->render('app::vacation-detail', [
            'vacation'       => $vacation,
            'itineraryItems' => $itineraryItems,
            'expenses'       => $expenses,
            'totalExpenses'  => $totalExpenses,
        ]));
    }
}
