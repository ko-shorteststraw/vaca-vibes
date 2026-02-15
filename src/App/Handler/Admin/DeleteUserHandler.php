<?php

declare(strict_types=1);

namespace App\Handler\Admin;

use App\Service\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use starfederation\datastar\ServerSentEventGenerator;

class DeleteUserHandler implements RequestHandlerInterface
{
    public function __construct(
        private UserRepository $userRepo,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $targetId = (int) $request->getAttribute('id');
        $currentUser = $request->getAttribute('user');

        $sse = new ServerSentEventGenerator();
        $sse->sendHeaders();

        if ($targetId === $currentUser['id']) {
            $sse->patchElements(
                '<div class="notification is-danger is-light" id="admin-error">You cannot delete your own account.</div>',
                ['selector' => '#admin-error-container'],
            );
            exit;
        }

        $this->userRepo->delete($targetId);
        $sse->removeElements('#user-row-' . $targetId);
        exit;
    }
}
