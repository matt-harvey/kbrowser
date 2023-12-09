<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;
use App\Route;

$kubernetes = getKubernetes();
$context = $_GET['context'] or die('No context specified');
$namespace = $_GET['namespace'] or die('No namespace specified');
$objectKind = ObjectKind::POD;
$podName = $_GET['pod'] or die('No pod specified');
$logs = $kubernetes->getPodLogs($context, $namespace, $podName);

$title = $podName;
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

<?php DefaultLayout::open($title, $breadcrumbs); ?>
    <div>
        <?php foreach ($logs as $logLine): ?>
            <p><?= h($logLine) ?></p>
        <?php endforeach; ?>
    </div>
<?php DefaultLayout::close(); ?>
