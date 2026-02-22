<?php

declare(strict_types=1);

namespace App;

use App\Service\Kubernetes;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;
use Psr\Log\LoggerInterface;
use SubstancePHP\Container\Container;
use SubstancePHP\HTTP\EnvironmentInterface;
use SubstancePHP\HTTP\ProviderInterface;
use Monolog\Level;
use Monolog\Logger;

class ApplicationProvider implements ProviderInterface
{
    #[\Override]
    public static function factories(EnvironmentInterface $environment): array
    {
        return [
            'app-name.formal' => fn () => 'kbrowser',

            Kubernetes::class => Container::autowire(...),

            'logger.filepath' => fn () => 'php://stderr',
            LoggerInterface::class => fn ($c) => $c->get(Logger::class),
            Logger::class => function (Container $c): Logger {
                $filepath = $c->get('logger.filepath');
                $channel = $c->get('app-name.formal') . '-logger';
                $logger = new Logger($channel);
                $logger->pushProcessor(new WebProcessor());
                $logger->pushProcessor(new UidProcessor());
                $streamHandler = new StreamHandler($filepath, Level::Debug);
                $logger->pushHandler($streamHandler);
                return $logger;
            },
        ];
    }
}