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

function simplifiedPodName(string $fullPodName): string
{
    return \preg_replace('/^pod\//', '', $fullPodName);
}

function simplifiedDeploymentName(string $fullDeploymentName): string
{
    return \preg_replace('/^.+\//', '', $fullDeploymentName);
}

function simplifiedDaemonSetName(string $fullDaemonSetName): string
{
    return \preg_replace('/^.+\//', '', $fullDaemonSetName);
}

function simplifiedStatefulSetName(string $fullStatefulSetName): string
{
    return \preg_replace('/^.+\//', '', $fullStatefulSetName);
}

function resourcesUrl(ObjectKind $resourceType, ?string $namespace = null): string
{
    $url = '/' . urlencode($resourceType->pluralSmallTitle());
    if ($namespace !== null) {
        $url .= '?' . \http_build_query(['namespace' => $namespace]);
    }
    return $url;
}

function namespaceUrl(string $namespace): string
{
    return '/namespace?' . \http_build_query(['namespace' => $namespace]);
}

function namespacedResourceUrl(ObjectKind $resourceType, string $resourceName, string $namespace): string
{
    $resourceTypeSmallTitle = $resourceType->smallTitle();
    $query = \http_build_query([
        ObjectKind::NAMESPACE->smallTitle() => $namespace,
        $resourceTypeSmallTitle => $resourceName,
    ]);
    return "/$resourceTypeSmallTitle?$query";
}

