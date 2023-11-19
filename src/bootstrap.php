<?php

declare(strict_types=1);

\define('SRC_ROOT', __DIR__);
\define('PROJECT_ROOT', \dirname(SRC_ROOT));
\define('ACTION_ROOT', SRC_ROOT . '/action');

require PROJECT_ROOT . '/vendor/autoload.php';

use App\ObjectKind;
use App\Service\Kubernetes;

// bootstrapping

\set_error_handler(function ($errorCode, $errorString) {
    echo $errorString;
    \http_response_code(500);
    die();
});

\set_exception_handler(function (\Throwable $exception) {
    echo \get_class($exception) . ': ' . $exception->getMessage();
    \http_response_code(500);
    die();
});

// global helper functions

function h(mixed $s): string
{
    return \htmlspecialchars($s);
}

function getCluster(): Kubernetes
{
    return new Kubernetes();
}

function simplifiedObjectName(string $fullObjectName): string
{
    return \preg_replace('/^.+\//', '', $fullObjectName);
}

function resourcesUrl(ObjectKind $kind, ?string $namespace = null): string
{
    $query = ['kind' => $kind->title()];
    if ($namespace !== null) {
        $query['namespace'] = $namespace;
    }
    return '/resources?' . \http_build_query($query);
}

function namespacesUrl(): string
{
    return '/namespaces';
}

function namespaceUrl(string $namespace): string
{
    return '/namespace?' . \http_build_query(['namespace' => $namespace]);
}

function namespacedResourceUrl(ObjectKind $resourceType, string $resourceName, string $namespace): string
{
    $query = [
        'namespace' => $namespace,
        'kind' => $resourceType->title(),
        'object' => $resourceName,
    ];
    return '/resource?' . \http_build_query($query);
}

