<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface
{
    public function __construct(private UserRepository $userRepo)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if ($userId !== null) {
            $user = $this->userRepo->findById((int) $userId);
            if ($user) {
                $request = $request->withAttribute('user', $user);
            } else {
                unset($_SESSION['user_id']);
            }
        }

        return $handler->handle($request);
    }
}
