<?php

declare(strict_types=1);

use App\ResourceType;
use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespace = $_GET['namespace'] ?? die('No namespace specified');
$daemonSet = $_GET['daemonset'] ?? die('No daemonset specified');
$daemonSetDescription = $cluster->describe(ResourceType::DAEMON_SET, $namespace, $daemonSet);

$title = $daemonSet;
$breadcrumbs = [
    [$cluster->getShortClusterName() => '/'],
    ['namespaces' => '/namespaces'],
    [$namespace => namespaceUrl($namespace)],
    ['daemonsets' => '/daemonsets?' . \http_build_query(['namespace' => $namespace])],
    [$daemonSet  => null],
];

?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
    <div>
        <pre>
<?= h($daemonSetDescription) ?>
        </pre>
    </div>
<?php DefaultLayout::close(); ?>
