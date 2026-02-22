<?php

declare(strict_types=1);

use App\Html\Breadcrumb;
use App\Html\Layout\DefaultLayout;
use App\Route;
use SubstancePHP\HTTP\Renderer\HtmlRenderer;

/**
 * @var HtmlRenderer $this
 * @var string $context
 * @var string $namespace
 * @var string $selector
 * @var string $title
 * @var string $order
 * @var string[] $logs
 * @var Breadcrumb[] $breadcrumbs
 */
?>

<?php if (DefaultLayout::open($this, $title, $breadcrumbs)): ?>
    <?php if (\count($logs) == 0): ?>
        <div>No logs available.</div>
    <?php else: ?>
        <div>
            <?php if ($order === 'newest-first'): ?>
                <span>
                    <b>Ordered new-to-old</b>
                </span>
                &nbsp;
                <span>
                    <button>
                        <a href="<?= Route::forSelectorLogs($context, $namespace, $selector, false) ?>">
                            Switch to old-to-new
                        </a>
                    </button>
                </span>
            <?php else: ?>
                <span>
                    <b>Ordered old-to-new</b>
                </span>
                &nbsp;
                <span>
                    <button>
                        <a href="<?= Route::forSelectorLogs($context, $namespace, $selector, true) ?>">
                            Switch to new-to-old
                        </a>
                    </button>
                </span>
            <?php endif; ?>
        </div>
        <br>

        <div>
            <?php foreach ($logs as $logLine): ?>
                <p><?= $this->e($logLine) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php DefaultLayout::close(); ?>
