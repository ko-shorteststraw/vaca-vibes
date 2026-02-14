<?php

declare(strict_types=1);

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

return [
    'dependencies' => [
        'abstract_factories' => [
            ReflectionBasedAbstractFactory::class,
        ],
        'factories' => [
            App\Service\DatabaseService::class => App\Service\DatabaseServiceFactory::class,
        ],
    ],
];
