<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;
use App\Route;

$kubernetes = getKubernetes();
$context = $_GET['context'] ?? die('No context specified');
$namespace = $_GET['namespace'] ?? null;
$objectKind = $_GET['kind'] ?? die('No object kind specified');
$objectKind = ObjectKind::from($objectKind);

$title = $objectKind->pluralTitle();

if ($namespace === null) {
    $table = $kubernetes->getObjectsTable($context, $objectKind, null, $objectKind->isNamespaced());
    $breadcrumbs = [
        Route::forHome()->toBreadcrumb(),
        Route::forContext($context)->toBreadcrumb(),
        [$objectKind->pluralSmallTitle() => null],
    ];
} else {
    $table = $kubernetes->getObjectsTable($context, $objectKind, $namespace, false);
    $breadcrumbs = [
        Route::forHome()->toBreadcrumb(),
        Route::forContext($context)->toBreadcrumb(),
        Route::forNamespaces($context)->toBreadcrumb(),
        Route::forNamespace($context, $namespace)->toBreadcrumb(),
        [$objectKind->pluralSmallTitle() => null],
    ];
}
?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
    <div>
        &nbsp;&nbsp;<?= \count($table) ?> <?= $objectKind->pluralSmallTitle() ?>:
    </div>
    <br>
    <div>
        <table>
            <thead>
                <tr>
                    <?php foreach ($table->getColumns() as $column): ?>
                        <td><b><?= h($column->getHeader()) ?></b></td>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($table as $row): ?>
                    <tr>
                        <?php foreach ($table->currentCells() as $cell): ?>
                            <td>
                                <?php if ($cell->key === 'namespace'): ?>
                                    <a href="<?= Route::forNamespace($context, $cell->contents) ?>">
                                        <?= h($cell->contents) ?>
                                    </a>
                                <?php elseif ($cell->key === 'name'): ?>
                                    <?php
                                        if ($objectKind->isNamespaced()) {
                                            $route = Route::forNamespacedResource(
                                                $context,
                                                $objectKind,
                                                $cell->contents,
                                                $cell->dataSource['metadata']['namespace'],
                                            );
                                        } elseif ($objectKind === ObjectKind::NAMESPACE) {
                                            $route = Route::forNamespace($context, $cell->contents);
                                        } else {
                                            $route = Route::forNonNamespacedResource(
                                                $context,
                                                $objectKind,
                                                $cell->contents,
                                            );
                                        }
                                    ?>

                                    <a href="<?= $route ?>"><?= h($cell->contents) ?></a>
                                <?php else: ?>
                                    <?= h($cell->contents) ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php DefaultLayout::close(); ?>
