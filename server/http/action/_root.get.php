<?php

declare(strict_types=1);

use App\Service\Kubernetes;
use App\Route;

return static function (Kubernetes $kubernetes): mixed {
    return [
        'title' => 'KBrowser',
        'breadcrumbs' => [Route::forHome()->toBreadcrumb(false)],
        'contexts' => $kubernetes->getContexts(),
    ];
};

