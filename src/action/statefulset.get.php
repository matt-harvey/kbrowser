<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ResourceType;

$cluster = getCluster();
$resourceType = ResourceType::STATEFUL_SET;
$namespace = $_GET['namespace'] ?? die('No namespace specified');
$statefulSet = $_GET[$resourceType->smallTitle()] ?? die("No {$resourceType->smallTitle()} specified");
$statefulSetDescription = $cluster->describe($resourceType, $namespace, $statefulSet);

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
