<?php

declare(strict_types=1);

use App\Exception\NotFoundException;
use App\Layout\DefaultLayout;
use App\ObjectKind;
use App\Route;

$kubernetes = getKubernetes();
$context = $_GET['context'] or die('No context specified');
$namespace = $_GET['namespace'] or die('No namespace specified');
$objectKind = ObjectKind::POD;
$podName = $_GET['pod'] or die('No pod specified');
$title = $podName;
$order = $_GET['order'] ?? 'newest-first';
$showNewestFirst = ($order === 'newest-first');
try {
    $logs = $kubernetes->getPodLogs($context, $namespace, $podName, $showNewestFirst);
    $errorMessage = null;
} catch (NotFoundException) {
    $logs = [];
    $errorMessage = 'Pod not found. Perhaps it has been deleted?';
    \http_response_code(404);
}

$breadcrumbs = [
    Route::forHome()->toBreadcrumb(),
    Route::forContext($context)->toBreadcrumb(),
    Route::forNamespaces($context)->toBreadcrumb(),
    Route::forNamespace($context, $namespace)->toBreadcrumb(),
    Route::forResources($context, $objectKind, $namespace)->toBreadcrumb(),
    Route::forNamespacedResource($context, $objectKind, $podName, $namespace)->toBreadcrumb(),
    ['logs' => null],
];
?>

<?php if ($errorMessage !== null): ?>
    <?php DefaultLayout::open($title, $breadcrumbs); ?>
    <p><?= h($errorMessage) ?></p>
    <?php DefaultLayout::close(); ?>
    <?php exit; ?>
<?php endif; ?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
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
                <p><?= h($logLine) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php DefaultLayout::close(); ?>