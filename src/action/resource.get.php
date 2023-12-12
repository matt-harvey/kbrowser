<?php

declare(strict_types=1);

use App\Exception\NotFoundException;
use App\Layout\DefaultLayout;
use App\ObjectKind;
use App\Route;

$kubernetes = getKubernetes();
$context = $_GET['context'] or die('No context specified');
$namespace = $_GET['namespace'] or die('No namespace specified');
$objectKind = $_GET['kind'] or die('No object kind specified');
$objectKind = ObjectKind::from($objectKind);
$objectName = $_GET['object'] or die('No object specified');
$title = $objectName;

try {
    $objectDescription = $kubernetes->describe($context, $objectKind, $namespace, $objectName);
    $responseCode = 200;
    $errorMessage = null;
} catch (NotFoundException) {
    $objectDescription = '';
    $responseCode = 404;
    $errorMessage = "{$objectKind->title()} not found. Perhaps it has been deleted?";
}

$lines = \explode(PHP_EOL, $objectDescription);
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
?>

<?php DefaultLayout::use($title, $breadcrumbs, $responseCode, $errorMessage); ?>

<b><?= h("{$objectKind->value}/$objectName") ?></b>
<?php if ($ownerUrl !== null): ?>
    [controlled by <a href="<?= $ownerUrl ?>"><?= h("$ownerKindStr/$ownerName") ?></a>]
<?php endif; ?>

<div>
    <pre>
<?= h($objectDescription) ?>
    </pre>
</div>

<?php if ($objectKind === ObjectKind::POD || \count($selectors) != 0): ?>
    <br>
    <b>Logs:</b>
<?php endif; ?>

<?php if ($objectKind === ObjectKind::POD): ?>
    <div>
        <br>
        For pod:
        <a href="<?= Route::forPodLogs($context, $namespace, $objectName, true) ?>">
            <?= h($objectName) ?>
        </a>
        <br>
    </div>
<?php endif; ?>

<?php foreach ($selectors as $selector): ?>
    <div>
        <br>
        For selector:
        <a href="<?= Route::forSelectorLogs($context, $namespace, $selector, true) ?>">
            <?= h($selector) ?>
        </a>
        <br>
    </div>
<?php endforeach; ?>
