<?php

declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    // Page routes
    $app->get('/', App\Handler\HomeHandler::class, 'home');
    $app->get('/vacation/{id:\d+}', App\Handler\VacationDetailHandler::class, 'vacation.detail');

    // Vacation API (SSE)
    $app->get('/api/vacation/form', App\Handler\Vacation\FormHandler::class, 'vacation.form.create');
    $app->get('/api/vacation/{id:\d+}/edit', App\Handler\Vacation\FormHandler::class, 'vacation.form.edit');
    $app->post('/api/vacation', App\Handler\Vacation\CreateHandler::class, 'vacation.create');
    $app->put('/api/vacation/{id:\d+}', App\Handler\Vacation\UpdateHandler::class, 'vacation.update');
    $app->delete('/api/vacation/{id:\d+}', App\Handler\Vacation\DeleteHandler::class, 'vacation.delete');

    // Itinerary API (SSE)
    $app->post('/api/vacation/{id:\d+}/itinerary', App\Handler\Itinerary\CreateHandler::class, 'itinerary.create');
    $app->delete('/api/itinerary/{id:\d+}', App\Handler\Itinerary\DeleteHandler::class, 'itinerary.delete');

    // Expense API (SSE)
    $app->post('/api/vacation/{id:\d+}/expense', App\Handler\Expense\CreateHandler::class, 'expense.create');
    $app->delete('/api/expense/{id:\d+}', App\Handler\Expense\DeleteHandler::class, 'expense.delete');
};
