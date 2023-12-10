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
foreach ($lines as $line) {
    if (\preg_match('/^(Controlled By):\s+([^\s]+)$/', $line, $matches)) {
        [$ownerKindStr, $ownerName] = \explode('/', $matches[2]);
        $ownerKind = ObjectKind::tryFrom($ownerKindStr);
        if ($ownerKind !== null) {
            $ownerUrl = match (true) {
                $ownerKind->isNamespaced() =>
                    Route::forNamespacedResource($context, $ownerKind, $ownerName, $namespace)->toUrl(),
                default =>
                    Route::forNonNamespacedResource($context, $ownerKind, $ownerName)->toUrl(),
            };
            break;
        }
    }
}

$breadcrumbs = [
    Route::forHome()->toBreadcrumb(),
    Route::forContext($context)->toBreadcrumb(),
    Route::forNamespaces($context)->toBreadcrumb(),
    Route::forNamespace($context, $namespace)->toBreadcrumb(),
    Route::forResources($context, $objectKind, $namespace)->toBreadcrumb(),
    [\strval($objectName) => null],
];
?>

<?php DefaultLayout::use($title, $breadcrumbs, $responseCode, $errorMessage); ?>

<b><?= h("{$objectKind->value}/$objectName") ?></b>
<?php if ($ownerUrl !== null): ?>
    [controlled by <a href="<?= $ownerUrl ?>"><?= h("$ownerKindStr/$ownerName") ?></a>]
<?php endif; ?>

<?php if ($objectKind === ObjectKind::POD): ?>
    <div>
        <br>
        <button>
            <a href="<?= Route::forPodLogs($context, $namespace, $objectName, true) ?>">
                View logs
            </a>
        </button>
        <br>
        <br>
    </div>
<?php endif; ?>

<div>
    <pre>
<?= h($objectDescription) ?>
    </pre>
</div>
