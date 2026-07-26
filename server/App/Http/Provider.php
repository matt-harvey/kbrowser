<?php

declare(strict_types=1);

namespace App\Http;

use Middlewares\Whoops;
use Psr\Container\ContainerInterface;
use SubstancePHP\Container\Container;
use SubstancePHP\HTTP\ContextFactory as HttpContextFactory;
use SubstancePHP\HTTP\ContextFactoryInterface as HttpContextFactoryInterface;
use SubstancePHP\HTTP\EnvironmentInterface;
use SubstancePHP\HTTP\ProviderInterface;

class Provider implements ProviderInterface
{
    #[\Override]
    public static function factories(EnvironmentInterface $environment): array
    {
        return [
            Container::class => fn ($c) => $c,
            ContainerInterface::class => fn ($c) => $c,
            EnvironmentInterface::class => fn () => $environment,
            Whoops::class => Container::autowire(...),
            HttpContextFactoryInterface::class => fn () => new HttpContextFactory(),
            'substance.http.default-content-type' => fn () => 'text/html',
        ];
    }
}
