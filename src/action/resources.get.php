<?php

declare(strict_types=1);

use App\CellStyle;
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
        Route::forResources($context, $objectKind, $namespace)->toBreadcrumb(false),
    ];
} else {
    $table = $kubernetes->getObjectsTable($context, $objectKind, $namespace, false);
    $breadcrumbs = [
        Route::forHome()->toBreadcrumb(),
        Route::forContext($context)->toBreadcrumb(),
        Route::forNamespaces($context)->toBreadcrumb(),
        Route::forNamespace($context, $namespace)->toBreadcrumb(),
        Route::forResources($context, $objectKind, $namespace)->toBreadcrumb(false),
    ];
}
?>

<?php DefaultLayout::use($title, $breadcrumbs); ?>

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
                    <?php foreach ($table->currentCells($context) as $cell): ?>
                        <?php if ($cell->style == CellStyle::BUTTON): ?>
                            <td>
                                <button>
                                    <?php if ($cell->url === null): ?>
                                        <?= h($cell->contents) ?>
                                    <?php else: ?>
                                        <a href="<?= $cell->url ?>"><?= h($cell->contents) ?></a>
                                    <?php endif; ?>
                                </button>
                            </td>
                         <?php else: ?>
                            <td style="text-align: <?= $cell->style->value ?>; align-content: <?= $cell->style->value ?>;">
                                <?php if ($cell->url === null): ?>
                                    <?= h($cell->contents) ?>
                                <?php else: ?>
                                    <a href="<?= $cell->url ?>"><?= h($cell->contents) ?></a>
                                <?php endif; ?>
                            </td>
                         <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>