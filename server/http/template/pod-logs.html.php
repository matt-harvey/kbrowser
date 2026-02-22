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
 * @var ?string $errorMessage
 * @var string $podName
 * @var string $title
 * @var Breadcrumb[] $breadcrumbs
 * @var string $order
 * @var string[] $logs
 */
?>

<?php if (DefaultLayout::open($this, $title, $breadcrumbs, $errorMessage)): ?>
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
                        <a href="<?= Route::forPodLogs($context, $namespace, $podName, false) ?>">
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
                        <a href="<?= Route::forPodLogs($context, $namespace, $podName, true) ?>">
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
