<?php

declare(strict_types=1);

use App\Exception\NotFoundException;
use App\Layout\DefaultLayout;
use App\ObjectKind;
use App\Route;

$kubernetes = getKubernetes();
$context = $_GET['context'] or die('Context not specified');
$namespace = $_GET['namespace'] or die('Namespace not specified');
$title = $namespace;
try {
    $namespaceDescription = $kubernetes->describe(
        $context,
        ObjectKind::NAMESPACE,
        null,
        $namespace,
    );
    $errorMessage = null;
    $responseCode = 200;
} catch (NotFoundException) {
    $namespaceDescription = '';
    $errorMessage = ObjectKind::NAMESPACE->title() . ' not found. Perhaps it has been deleted?';
    $responseCode = 404;
}

$breadcrumbs = [
    Route::forHome()->toBreadcrumb(),
    Route::forContext($context)->toBreadcrumb(),
    Route::forNamespaces($context)->toBreadcrumb(),
    Route::forNamespace($context, $namespace)->toBreadcrumb(false),
];

?>

<?php DefaultLayout::use($title, $breadcrumbs, $responseCode, $errorMessage) ?>

<div>
    <pre>
<?= h($namespaceDescription) ?>
    </pre>
</div>

<hr>

<?php foreach (ObjectKind::cases() as $resourceType): ?>
    <?php if ($resourceType->isNamespaced()): ?>
        <p>
            <?= h($namespace) ?> &rightarrow;

            <a href="<?= Route::forResources($context, $resourceType, $namespace) ?>">
                <?= h($resourceType->pluralTitle()) ?>
            </a>
        </p>
    <?php endif; ?>
<?php endforeach; ?>
