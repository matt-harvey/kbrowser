<?php

declare(strict_types=1);

\define('SRC_ROOT', __DIR__);
\define('PROJECT_ROOT', \dirname(SRC_ROOT));
\define('ACTION_ROOT', SRC_ROOT . '/action');

require PROJECT_ROOT . '/vendor/autoload.php';

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

function namespaceUrl(string $namespace): string
{
    return '/namespace?' . \http_build_query(['namespace' => $namespace]);
}

function podUrl(string $pod, string $namespace): string
{
    $data = ['pod' => \preg_replace('/^pod\//', '', $pod)];
    if ($namespace !== null) {
        $data['namespace'] = $namespace;
    }
    $query = \http_build_query($data);
    return "/pod?$query";
}

function deploymentUrl(string $deployment, ?string $namespace): string
{
    $data = ['deployment' => $deployment];
    if ($namespace !== null) {
        $data['namespace'] = $namespace;
    }
    $query = \http_build_query($data);
    return "/deployment?$query";
}

function daemonSetUrl(string $daemonSet, ?string $namespace): string
{
    $data = ['daemonSet' => $daemonSet];
    if ($namespace !== null) {
        $data['namespace'] = $namespace;
    }
    $query = \http_build_query($data);
    return "/daemonset?$query";
}

