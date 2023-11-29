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