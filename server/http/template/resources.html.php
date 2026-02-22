<?php

declare(strict_types=1);

use App\Enum\ObjectKind;
use App\Html\Breadcrumb;
use App\Html\CellStyle;
use App\Html\Layout\DefaultLayout;
use App\Table;
use SubstancePHP\HTTP\Renderer\HtmlRenderer;

/**
 * @var HtmlRenderer $this
 * @var string $title
 * @var Breadcrumb[] $breadcrumbs
 * @var string $context
 * @var Table $table
 * @var ObjectKind $objectKind
 */
?>

<?php if (DefaultLayout::open($this, $title, $breadcrumbs)): ?>

    <div>
        &nbsp;&nbsp;<?= \count($table) ?> <?= $objectKind->pluralSmallTitle() ?>:
    </div>
    <br>
    <div>
        <table>
            <thead>
                <tr>
                    <?php foreach ($table->getColumns() as $column): ?>
                        <td><b><?= $this->e($column->getHeader()) ?></b></td>
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
                                            <?= $this->e($cell->contents) ?>
                                        <?php else: ?>
                                            <a href="<?= $cell->url ?>"><?= $this->e($cell->contents) ?></a>
                                        <?php endif; ?>
                                    </button>
                                </td>
                             <?php else: ?>
                                <td style="text-align: <?= $cell->style->value ?>; align-content: <?= $cell->style->value ?>;">
                                    <?php if ($cell->url === null): ?>
                                        <?= $this->e($cell->contents) ?>
                                    <?php else: ?>
                                        <a href="<?= $cell->url ?>"><?= $this->e($cell->contents) ?></a>
                                    <?php endif; ?>
                                </td>
                             <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php DefaultLayout::close() ?>
