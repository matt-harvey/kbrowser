<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;
use App\Route;

$cluster = getKubernetes();
$title = 'KBrowser';
$context = $_GET['context'] ?? die('Context not provided');
$breadcrumbs = [
    Route::forHome()->toBreadcrumb(),
    Route::forContext($context)->toBreadcrumb(false),
];
?>

<?php DefaultLayout::use($title, $breadcrumbs) ?>

<?php foreach (ObjectKind::cases() as $objectKind): ?>

    <?php
        [$url, $linkText] = match ($objectKind) {
            ObjectKind::NAMESPACE => [Route::forNamespaces($context)->toUrl(), 'Namespaces'],
            default => [Route::forResources($context, $objectKind)->toUrl(), $objectKind->pluralTitle()],
        };
    ?>

    <p>
        <a href="<?= $url ?>"><?= h($linkText) ?></a>
    </p>

<?php endforeach; ?>