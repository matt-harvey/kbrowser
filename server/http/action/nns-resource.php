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
    $objectKind = ObjectKind::tryFrom($query['kind'] ?? '') ?? UserError::throw(422, 'Missing object kind');
    $objectName = $query['object'] ?? UserError::throw(422, 'No object specified');
    $title = $objectName;
    try {
        $objectDescription = $kubernetes->describe($context, $objectKind, null, $objectName);
        $errorMessage = null;
    } catch (NotFoundException) {
        $objectDescription = '';
        $respond(404);
        $errorMessage = "{$objectKind->title()} not found. Perhaps it has been deleted?";
    }

    $breadcrumbs = [
        Route::forHome()->toBreadcrumb(),
        Route::forContext($context)->toBreadcrumb(),
        Route::forResources($context, $objectKind)->toBreadcrumb(),
        Route::forNonNamespacedResource($context, $objectKind, $objectName)->toBreadcrumb(false),
    ];

    return \compact('title', 'breadcrumbs', 'errorMessage', 'objectDescription');
};

