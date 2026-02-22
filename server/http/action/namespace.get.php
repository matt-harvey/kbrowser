<?php

declare(strict_types=1);

use App\Enum\ObjectKind;
use App\Exception\NotFoundException;
use App\Route;
use App\Service\Kubernetes;
use SubstancePHP\HTTP\Exception\BaseException\UserError;
use SubstancePHP\HTTP\RequestParams\QueryParams;
use SubstancePHP\HTTP\Respond;

return static function (QueryParams $query, Respond $respond, Kubernetes $kubernetes): mixed {
    $context = $query['context'] ?? UserError::throw(422, 'Context not specified');
    $namespace = $query['namespace'] ?? UserError::throw(422, 'Namespace not specified');
    $title = $namespace;
    try {
        $namespaceDescription = $kubernetes->describe($context, ObjectKind::NAMESPACE, null, $namespace);
        $errorMessage = null;
    } catch (NotFoundException) {
        $namespaceDescription = '';
        $errorMessage = ObjectKind::NAMESPACE->title() . ' not found. Perhaps it has been deleted?';
        $respond(404);
    }

    $breadcrumbs = [
        Route::forHome()->toBreadcrumb(),
        Route::forContext($context)->toBreadcrumb(),
        Route::forNamespaces($context)->toBreadcrumb(),
        Route::forNamespace($context, $namespace)->toBreadcrumb(false),
    ];

    return \compact('context', 'namespace', 'title', 'namespaceDescription', 'errorMessage', 'breadcrumbs');
};