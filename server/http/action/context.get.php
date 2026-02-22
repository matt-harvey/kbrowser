<?php

declare(strict_types=1);

use App\Route;
use SubstancePHP\HTTP\Exception\BaseException\UserError;
use SubstancePHP\HTTP\RequestParams\QueryParams;

return static function (QueryParams $query): mixed {
    $context = $query['context'] ?? UserError::throw(422, 'Missing context');
    $title = 'KBrowser';
    $breadcrumbs = [
        Route::forHome()->toBreadcrumb(),
        Route::forContext($context)->toBreadcrumb(false),
    ];
    return \compact('breadcrumbs', 'title', 'context');
};