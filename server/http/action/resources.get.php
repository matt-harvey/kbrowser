<?php

declare(strict_types=1);

use App\Enum\ObjectKind;
use App\Route;
use App\Service\Kubernetes;
use SubstancePHP\HTTP\Exception\BaseException\UserError;
use SubstancePHP\HTTP\RequestParams\QueryParams;

return static function (QueryParams $query, Kubernetes $kubernetes): mixed {
    $context = $query['context'] ?? UserError::throw(422, 'Missing context');
    $namespace = $query['namespace'] ?? Null;
    $objectKind = ObjectKind::tryFrom($query['kind'] ?? '') ?? UserError::throw(422, 'Missing object kind');
    $title = $objectKind->pluralTitle();

    if ($namespace === null) {
        $table = $kubernetes->getObjectsTable($context, $objectKind, null, $objectKind->isNamespaced());
        $breadcrumbs = [
            Route::forHome()->toBreadcrumb(),
            Route::forContext($context)->toBreadcrumb(),
            Route::forResources($context, $objectKind, $namespace)->toBreadcrumb(false),
        ];
    } else {
        $table = $kubernetes->getObjectsTable($context, $objectKind, $namespace, false);
        $breadcrumbs = [
            Route::forHome()->toBreadcrumb(),
            Route::forContext($context)->toBreadcrumb(),
            Route::forNamespaces($context)->toBreadcrumb(),
            Route::forNamespace($context, $namespace)->toBreadcrumb(),
            Route::forResources($context, $objectKind, $namespace)->toBreadcrumb(false),
        ];
    }

    return \compact('breadcrumbs', 'title', 'context', 'objectKind', 'table');
};