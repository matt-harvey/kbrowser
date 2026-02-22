<?php

declare(strict_types=1);

use App\Enum\ObjectKind;
use App\Exception\NotFoundException;
use App\Route;
use App\Service\Kubernetes;
use SubstancePHP\HTTP\Exception\BaseException\UserError;
use SubstancePHP\HTTP\RequestParams\QueryParams;
use SubstancePHP\HTTP\Respond;

return static function (QueryParams $query, Kubernetes $kubernetes, Respond $respond): mixed {
    $context = $query['context'] ?? UserError::throw(422, 'Missing context');
    $namespace = $query['namespace'] ?? UserError::throw(422, 'Namespace not specified');
    $objectKind = ObjectKind::tryFrom($query['kind'] ?? '') ?? UserError::throw(422, 'Missing object kind');
    $objectName = $query['object'] ?? UserError::throw(422, 'No object specified');
    $title = $objectKind->pluralTitle();
    try {
        $objectDescription = $kubernetes->describe($context, $objectKind, $namespace, $objectName);
        $errorMessage = null;
    } catch (NotFoundException) {
        $objectDescription = '';
        $respond(404);
        $errorMessage = "{$objectKind->title()} not found. Perhaps it has been deleted?";
    }
    $lines = \explode(\PHP_EOL, $objectDescription);
    $ownerUrl = null;
    $ownerName = null;
    $ownerKindStr = null;
    $selectors = [];
    foreach ($lines as $line) {
        if ($ownerName !== null && $selectors !== null) {
            break;
        }
        if (\preg_match('/^Controlled By:\s+([^\s]+)$/', $line, $matches)) {
            [$ownerKindStr, $ownerName] = \explode('/', $matches[1]);
            $ownerKind = ObjectKind::tryFrom($ownerKindStr);
            if ($ownerKind !== null) {
                $ownerUrl = match (true) {
                    $ownerKind->isNamespaced() =>
                    Route::forNamespacedResource($context, $ownerKind, $ownerName, $namespace)->toUrl(),
                    default =>
                    Route::forNonNamespacedResource($context, $ownerKind, $ownerName)->toUrl(),
                };
            }
        }
        if (\preg_match('/^Selector:\s+([^\s]+)$/', $line, $matches)) {
            $selectors = [];
            $segments = \explode(',', $matches[1]);
            foreach ($segments as $segment) {
                $segment = \trim($segment);
                if ($segment !== '<unset>') {
                    $selectors[] = $segment;
                }
            }
        }
    }
    $breadcrumbs = [
            Route::forHome()->toBreadcrumb(),
            Route::forContext($context)->toBreadcrumb(),
            Route::forNamespaces($context)->toBreadcrumb(),
            Route::forNamespace($context, $namespace)->toBreadcrumb(),
            Route::forResources($context, $objectKind, $namespace)->toBreadcrumb(),
            Route::forNamespacedResource($context, $objectKind, $objectName, $namespace)->toBreadcrumb(false),
    ];
    return \compact(
        'title',
        'breadcrumbs',
        'namespace',
        'objectKind',
        'objectName',
        'context',
        'ownerUrl',
        'errorMessage',
        'objectDescription',
        'ownerKindStr',
        'ownerName',
        'selectors',
    );
};

