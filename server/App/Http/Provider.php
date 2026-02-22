<?php

declare(strict_types=1);

namespace App\Http;

// use App\Http\Middleware\ErrorHandlerMiddleware; // FIXNOW
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
            // ErrorHandlerMiddleware::class => Container::autowire(...), // FIXNOW
            HttpContextFactoryInterface::class => fn () => new HttpContextFactory(),
            'substance.http.default-content-type' => fn () => 'text/html',
        ];
    }
}