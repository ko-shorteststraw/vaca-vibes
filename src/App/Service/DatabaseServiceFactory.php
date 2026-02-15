<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Container\ContainerInterface;

class DatabaseServiceFactory
{
    public function __invoke(ContainerInterface $container): DatabaseService
    {
        $dbPath = getcwd() . '/data/database.sqlite';
        $adminUsername = getenv('ADMIN_USERNAME') ?: 'admin';
        $adminPassword = getenv('ADMIN_PASSWORD') ?: 'admin';

        return new DatabaseService($dbPath, $adminUsername, $adminPassword);
    }
}
