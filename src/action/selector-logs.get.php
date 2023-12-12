<?php

declare(strict_types=1);

use App\Breadcrumb;
use App\Layout\DefaultLayout;
use App\Route;

$kubernetes = getKubernetes();
$context = $_GET['context'] or die('No context specified');
$namespace = $_GET['namespace'] or die('No namespace specified');
$selector = $_GET['selector'] or die('No pod specified');
$title = "Logs for $selector";
$order = $_GET['order'] ?? 'newest-first';
$showNewestFirst = ($order === 'newest-first');
$logs = $kubernetes->getSelectorLogs($context, $namespace, $selector, $showNewestFirst);

$breadcrumbs = [
    Route::forHome()->toBreadcrumb(),
    Route::forContext($context)->toBreadcrumb(),
    Route::forNamespaces($context)->toBreadcrumb(),
    Route::forNamespace($context, $namespace)->toBreadcrumb(),
    new Breadcrumb('logs', null),
    new Breadcrumb('selector', null),
];
?>

<?php DefaultLayout::use($title, $breadcrumbs); ?>

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
            <p><?= h($logLine) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>