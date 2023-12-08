<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;
use App\Route;

$kubernetes = getKubernetes();
$context = $_GET['context'] or die('No context specified');
$namespace = $_GET['namespace'] or die('No namespace specified');
$objectKind = $_GET['kind'] or die('No object kind specified');
$objectKind = ObjectKind::from($objectKind);
$objectName = $_GET['object'] or die('No object specified');
$objectDescription = $kubernetes->describe($context, $objectKind, $namespace, $objectName);

$lines = \explode(PHP_EOL, $objectDescription);
$ownerUrl = null;
$ownerName = null;
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

$title = $objectName;
$breadcrumbs = [
    Route::forHome()->toBreadcrumb(),
    Route::forContext($context)->toBreadcrumb(),
    Route::forNamespaces($context)->toBreadcrumb(),
    Route::forNamespace($context, $namespace)->toBreadcrumb(),
    Route::forResources($context, $objectKind, $namespace)->toBreadcrumb(),
    [\strval($objectName) => null],
];
?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
        <b><?= h("{$objectKind->value}/$objectName") ?></b>
        <?php if ($ownerUrl !== null): ?>
            [controlled by <a href="<?= $ownerUrl ?>"><?= h("$ownerKindStr/$ownerName") ?></a>]
        <?php endif; ?>
    <div>
        <pre>
<?= h($objectDescription) ?>
        </pre>
    </div>
<?php DefaultLayout::close(); ?>
