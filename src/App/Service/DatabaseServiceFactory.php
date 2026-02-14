<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Container\ContainerInterface;

class DatabaseServiceFactory
{
    public function __invoke(ContainerInterface $container): DatabaseService
    {
        $dbPath = getcwd() . '/data/database.sqlite';
        return new DatabaseService($dbPath);
    }
}
