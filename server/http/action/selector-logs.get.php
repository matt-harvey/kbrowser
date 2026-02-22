<?php

declare(strict_types=1);

use App\Enum\ObjectKind;
use App\Html\Breadcrumb;
use App\Route;
use App\Service\Kubernetes;
use SubstancePHP\HTTP\Exception\BaseException\UserError;
use SubstancePHP\HTTP\RequestParams\QueryParams;

return static function (QueryParams $query, Kubernetes $kubernetes): mixed {
    $context = $query['context'] ?? UserError::throw(422, 'Missing context');
    $namespace = $query['namespace'] ?? UserError::throw(422, 'Missing namespace');
    $selector = $query['selector'] ?? UserError::throw(422, 'Missing selector');
    $title = "Logs for $selector";
    $order = $query['order'] ?? 'newest-first';
    $showNewestFirst = ($order === 'newest-first');
    $logs = $kubernetes->getSelectorLogs($context, $namespace, $selector, $showNewestFirst);

    $breadcrumbs = [
        Route::forHome()->toBreadcrumb(),
        Route::forContext($context)->toBreadcrumb(),
        Route::forNamespaces($context)->toBreadcrumb(),
        Route::forNamespace($context, $namespace)->toBreadcrumb(),
        new Breadcrumb('logs', null),
        new Breadcrumb($selector, null),
    ];

    return \compact('context', 'namespace', 'selector', 'title', 'breadcrumbs', 'order', 'logs');
};