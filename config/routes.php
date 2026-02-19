<?php

declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    // Auth routes (public)
    $app->route('/login', App\Handler\Auth\LoginHandler::class, ['GET', 'POST'], 'login');
    $app->route('/register', App\Handler\Auth\RegisterHandler::class, ['GET', 'POST'], 'register');
    $app->post('/logout', App\Handler\Auth\LogoutHandler::class, 'logout');

    // Admin routes
    $app->get('/admin', [App\Middleware\AdminMiddleware::class, App\Handler\Admin\DashboardHandler::class], 'admin.dashboard');
    $app->get('/admin/users', [App\Middleware\AdminMiddleware::class, App\Handler\Admin\UsersHandler::class], 'admin.users');
    $app->delete('/api/admin/user/{id:\d+}', [App\Middleware\AdminMiddleware::class, App\Handler\Admin\DeleteUserHandler::class], 'admin.user.delete');

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
    $app->get('/api/vacation/{id:\d+}/itinerary/suggestions', App\Handler\Itinerary\SuggestHandler::class, 'itinerary.suggestions');
    $app->post('/api/vacation/{id:\d+}/itinerary/suggested', App\Handler\Itinerary\AddSuggestedHandler::class, 'itinerary.add-suggested');
    $app->get('/api/itinerary/{id:\d+}/edit', App\Handler\Itinerary\EditFormHandler::class, 'itinerary.edit');
    $app->put('/api/itinerary/{id:\d+}', App\Handler\Itinerary\UpdateHandler::class, 'itinerary.update');
    $app->get('/api/itinerary/{id:\d+}/cancel', App\Handler\Itinerary\CancelEditHandler::class, 'itinerary.cancel-edit');
    $app->delete('/api/itinerary/{id:\d+}', App\Handler\Itinerary\DeleteHandler::class, 'itinerary.delete');

    // Expense API (SSE)
    $app->post('/api/vacation/{id:\d+}/expense', App\Handler\Expense\CreateHandler::class, 'expense.create');
    $app->delete('/api/expense/{id:\d+}', App\Handler\Expense\DeleteHandler::class, 'expense.delete');
};
