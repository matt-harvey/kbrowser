<?php

declare(strict_types=1);

use App\ApplicationProvider;
use App\Http\Provider as HttpProvider;
use Middlewares\Whoops;
use Psr\Log\LoggerInterface;
use SubstancePHP\HTTP\Application;
use SubstancePHP\HTTP\Middleware\RouteActorMiddleware;
use SubstancePHP\HTTP\Middleware\RouteMatcherMiddleware;
use SubstancePHP\HTTP\SubstanceProvider;

define('SUBSTANCE_START_NANOSECONDS', \intval(\hrtime(true)));

require \dirname(__DIR__) . '/bootstrap.php';

/**
 * There is no authentication, no rate limiter, etc. because this application is intended to be
 * run locally, only.
 */
$application = Application::make(
    env: $_ENV,
    actionRoot: HTTP_ACTION_ROOT,
    templateRoot: HTTP_TEMPLATE_ROOT,
    providers: [
        SubstanceProvider::class,
        ApplicationProvider::class,
        HttpProvider::class,
    ],
    middlewares: [
        Whoops::class,
        RouteMatcherMiddleware::class,
        RouteActorMiddleware::class,
    ],
);
$application->execute();

$milliseconds = (\hrtime(true) - SUBSTANCE_START_NANOSECONDS) / 1_000_000.0;
$status = \http_response_code();
$application->get(LoggerInterface::class)->info('Completed', \compact('status', 'milliseconds'));
