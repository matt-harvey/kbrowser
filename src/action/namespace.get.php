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
} catch (NotFoundException) {
    $namespaceDescription = '';
    $errorMessage = ObjectKind::NAMESPACE->title() . ' not found. Perhaps it has been deleted?';
    \http_response_code(404);
}

$breadcrumbs = [
    Route::forHome()->toBreadcrumb(),
    Route::forContext($context)->toBreadcrumb(),
    Route::forNamespaces($context)->toBreadcrumb(),
    [\strval($namespace) => null],
];

?>

<?php if ($errorMessage !== null): ?>
    <?php DefaultLayout::open($title, $breadcrumbs); ?>
    <p><?= h($errorMessage) ?></p>
    <?php DefaultLayout::close(); ?>
    <?php exit; ?>
<?php endif; ?>

<?php DefaultLayout::open($title, $breadcrumbs) ?>
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
<?php DefaultLayout::close(); ?>
