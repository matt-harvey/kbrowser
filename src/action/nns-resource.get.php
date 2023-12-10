<?php

declare(strict_types=1);

use App\Exception\NotFoundException;
use App\Layout\DefaultLayout;
use App\ObjectKind;
use App\Route;

$kubernetes = getKubernetes();
$context = $_GET['context'] or die('No context specified');
$objectKind = $_GET['kind'] or die('No object kind specified');
$objectKind = ObjectKind::from($objectKind);
$objectName = $_GET['object'] or die('No object specified');
$title = $objectName;
try {
    $objectDescription = $kubernetes->describe($context, $objectKind, null, $objectName);
    $responseCode = 200;
    $errorMessage = null;
} catch (NotFoundException) {
    $objectDescription = '';
    $responseCode = 404;
    $errorMessage = "{$objectKind->title()} not found. Perhaps it has been deleted?";
}

$breadcrumbs = [
    Route::forHome()->toBreadcrumb(),
    Route::forContext($context)->toBreadcrumb(),
    Route::forResources($context, $objectKind)->toBreadcrumb(),
    [\strval($objectName)  => null],
];
?>

<?php DefaultLayout::use($title, $breadcrumbs, $responseCode, $errorMessage); ?>

<div>
    <pre>
<?= h($objectDescription) ?>
    </pre>
</div>
