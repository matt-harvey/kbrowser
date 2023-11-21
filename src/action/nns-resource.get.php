<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;

$cluster = getCluster();
$objectKind = $_GET['kind'] or die('No object kind specified');
$objectKind = ObjectKind::from($objectKind);
$objectName = $_GET['object'] or die('No object specified');
$objectDescription = $cluster->describe($objectKind, null, $objectName);

$title = $objectName;
$breadcrumbs = [
    [$cluster->getShortClusterName() => '/'],
    [$objectKind->pluralSmallTitle() => resourcesUrl($objectKind, null)],
    [$objectName  => null],
];
?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
    <div>
        <pre>
<?= h($objectDescription) ?>
        </pre>
    </div>
<?php DefaultLayout::close(); ?>
