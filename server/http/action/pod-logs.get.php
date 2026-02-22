<?php

declare(strict_types=1);

use App\Enum\ObjectKind;
use App\Exception\NotFoundException;
use App\Html\Breadcrumb;
use App\Route;
use App\Service\Kubernetes;
use SubstancePHP\HTTP\Exception\BaseException\UserError;
use SubstancePHP\HTTP\RequestParams\QueryParams;
use SubstancePHP\HTTP\Respond;

return static function (QueryParams $query, Respond $respond, Kubernetes $kubernetes): mixed {
    $context = $query['context'] ?? UserError::throw(422, 'Missing context');
    $namespace = $query['namespace'] ?? UserError::throw(422, 'Missing namespace');
    $objectKind = ObjectKind::POD;
    $podName = $query['pod'] ?? UserError::throw(422, 'Missing pod name');
    $title = $podName;
    $order = $query['order'] ?? 'newest-first';
    $showNewestFirst = ($order === 'newest-first');
    try {
        $logs = $kubernetes->getPodLogs($context, $namespace, $podName, $showNewestFirst);
        $errorMessage = null;
    } catch (NotFoundException) {
        $logs = [];
        $errorMessage = 'Pod not found. Perhaps it has been deleted?';
        $respond(404);
    }

    $breadcrumbs = [
        Route::forHome()->toBreadcrumb(),
        Route::forContext($context)->toBreadcrumb(),
        Route::forNamespaces($context)->toBreadcrumb(),
        Route::forNamespace($context, $namespace)->toBreadcrumb(),
        Route::forResources($context, $objectKind, $namespace)->toBreadcrumb(),
        Route::forNamespacedResource($context, $objectKind, $podName, $namespace)->toBreadcrumb(),
        new Breadcrumb('logs', null),
    ];

    return \compact('context', 'namespace', 'errorMessage', 'podName', 'title', 'breadcrumbs', 'order', 'logs');
};