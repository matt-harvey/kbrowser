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

$title = $objectName;
$breadcrumbs = [
    Route::forHome()->toBreadcrumb(),
    Route::forContext($context)->toBreadcrumb(),
    Route::forNamespaces($context)->toBreadcrumb(),
    Route::forNamespace($context, $namespace)->toBreadcrumb(),
    Route::forResources($context, $objectKind, $namespace)->toBreadcrumb(),
    [\strval($objectName)  => null],
];
?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
    <div>
        <pre>
<?= h($objectDescription) ?>
        </pre>
    </div>
<?php DefaultLayout::close(); ?>
