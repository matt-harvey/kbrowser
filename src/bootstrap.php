<?php

declare(strict_types=1);

\define('SRC_ROOT', __DIR__);
\define('PROJECT_ROOT', \dirname(SRC_ROOT));
\define('ACTION_ROOT', SRC_ROOT . '/action');
\define('HOME_CHAR', \json_decode('"\u2302"'));

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

function getKubernetes(): Kubernetes
{
    return new Kubernetes();
}

function simplifiedObjectName(string $fullObjectName): string
{
    return \preg_replace('|^.+/|', '', $fullObjectName);
}

function simplifiedContextName(string $fullName): string
{
    return \preg_replace('|^.+/|', '', $fullName);
}

function contextUrl(string $context): string
{
    return '/context?' . \http_build_query(['context' => $context]);
}

function resourcesUrl(
    string $context,
    ObjectKind $kind,
    ?string $namespace = null,
): string
{
    $query = ['context' => $context, 'kind' => $kind->title()];
    if ($namespace !== null) {
        $query['namespace'] = $namespace;
    }
    return '/resources?' . \http_build_query($query);
}

function namespacesUrl($context): string
{
    return '/namespaces?' . \http_build_query(['context' => $context]);
}

function namespaceUrl(string $context, string $namespace): string
{
    return '/namespace?' . \http_build_query([
        'context' => $context,
        'namespace' => $namespace,
    ]);
}

function nonNamespacedResourceUrl(string $context, ObjectKind $resourceType, string $resourceName): string
{
    $query = [
        'context' => $context,
        'kind' => $resourceType->title(),
        'object' => $resourceName,
    ];
    return '/nns-resource?' . \http_build_query($query);
}

function namespacedResourceUrl(string $context, ObjectKind $resourceType, string $resourceName, string $namespace): string
{
    $query = [
        'context' => $context,
        'namespace' => $namespace,
        'kind' => $resourceType->title(),
        'object' => $resourceName,
    ];
    return '/resource?' . \http_build_query($query);
}

function rootUrl(): string
{
    return '/';
}
