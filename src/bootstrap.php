<?php

declare(strict_types=1);

\define('SRC_ROOT', __DIR__);
\define('PROJECT_ROOT', \dirname(SRC_ROOT));
\define('ACTION_ROOT', SRC_ROOT . '/action');

require PROJECT_ROOT . '/vendor/autoload.php';

use App\ResourceType;
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

function resourcesUrl(ResourceType $resourceType, ?string $namespace = null): string
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

function podUrl(string $pod, string $namespace): string
{
    return namespacedResourceUrl(ResourceType::POD, $pod, $namespace);
}

function deploymentUrl(string $deployment, string $namespace): string
{
    return namespacedResourceUrl(ResourceType::DEPLOYMENT, $deployment, $namespace);
}

function daemonSetUrl(string $daemonSet, string $namespace): string
{
    return namespacedResourceUrl(ResourceType::DAEMON_SET, $daemonSet, $namespace);
}

function statefulSetUrl(string $statefulSet, string $namespace): string
{
    return namespacedResourceUrl(ResourceType::STATEFUL_SET, $statefulSet, $namespace);
}

function namespacedResourceUrl(ResourceType $resourceType, string $resourceName, string $namespace): string
{
    $resourceTypeSmallTitle = $resourceType->smallTitle();
    $query = \http_build_query([
        ResourceType::NAMESPACE->smallTitle() => $namespace,
        $resourceTypeSmallTitle => $resourceName,
    ]);
    return "/$resourceTypeSmallTitle?$query";
}

