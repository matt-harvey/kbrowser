<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespace = $_GET['namespace'] ?? die('No namespace specified');
$statefulSet = $_GET['statefulSet'] ?? die('No statefulset specified');
$statefulSetDescription = $cluster->describeStatefulSet($statefulSet, $namespace);

$title = $statefulSet;
$breadcrumbs = [
    [$cluster->getShortClusterName() => '/'],
    ['namespaces' => '/namespaces'],
    [$namespace => namespaceUrl($namespace)],
    ['statefulsets' => '/statefulsets?' . \http_build_query(['namespace' => $namespace])],
    [$statefulSet  => null],
];

?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
    <div>
        <pre>
<?= h($statefulSetDescription) ?>
        </pre>
    </div>
<?php DefaultLayout::close(); ?>
