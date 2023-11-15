<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespace = $_GET['namespace'] or die('Namespace not specified');

$title = 'Namespaces';
$breadcrumbs = [
    [$cluster->getShortClusterName() => '/'],
    ['namespaces' => '/namespaces'],
    [$namespace => null],
];

$podsPath = '/pods?' . \http_build_query(['namespace' => $namespace]);
$deploymentsPath = '/deployments?' . \http_build_query(['namespace' => $namespace]);
$daemonSetsPath = '/daemonsets?' . \http_build_query(['namespace' => $namespace]);
$statefulSetsPath = '/statefulsets?' . \http_build_query(['namespace' => $namespace]);

?>

<?php DefaultLayout::open($title, $breadcrumbs) ?>
    <p>
        <a href="<?= $podsPath ?>">Pods</a>
    </p>
    <p>
        <a href="<?= $deploymentsPath ?>">Deployments</a>
    </p>
    <p>
        <a href="<?= $daemonSetsPath ?>">DaemonSets</a>
    </p>
    <p>
        <a href="<?= $statefulSetsPath ?>">StatefulSets</a>
    </p>
<?php DefaultLayout::close(); ?>
