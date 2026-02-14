<?php

declare(strict_types=1);

use Laminas\ServiceManager\ServiceManager;

$providers = [
    Mezzio\ConfigProvider::class,
    Mezzio\Router\ConfigProvider::class,
    Mezzio\Router\FastRouteRouter\ConfigProvider::class,
    Mezzio\Plates\ConfigProvider::class,
    Mezzio\Helper\ConfigProvider::class,
    Laminas\Diactoros\ConfigProvider::class,
    Laminas\HttpHandlerRunner\ConfigProvider::class,
];

$config = [];
foreach ($providers as $provider) {
    $config = array_merge_recursive($config, (new $provider())());
}

// Merge local config
$config = array_merge_recursive(
    $config,
    require __DIR__ . '/autoload/dependencies.global.php',
    require __DIR__ . '/autoload/templates.global.php',
);

$dependencies = $config['dependencies'] ?? [];
$dependencies['services']['config'] = $config;

return new ServiceManager($dependencies);
