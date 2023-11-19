<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;

$cluster = getCluster();
$namespace = $_GET['namespace'] or die('No namespace specified');
$objectKind = $_GET['kind'] or die('No object kind specified');
$objectKind = ObjectKind::from($objectKind);
$objectName = $_GET['object'] or die('No object specified');
$objectDescription = $cluster->describe(ObjectKind::POD, $namespace, $objectName);

$title = $objectName;
$breadcrumbs = [
    [$cluster->getShortClusterName() => '/'],
    ['namespaces' => '/namespaces'],
    [$namespace => namespaceUrl($namespace)],
    [$objectKind->pluralSmallTitle() => resourcesUrl($objectKind, $namespace)],
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
