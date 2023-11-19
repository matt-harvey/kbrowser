<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;

$cluster = getCluster();
$namespace = $_GET['namespace'] or die('No namespace specified');
$deployment = $_GET['deployment'] or die('No deployment specified');
$deploymentDescription = $cluster->describe(ObjectKind::DEPLOYMENT, $namespace, $deployment);

$title = $deployment;
$breadcrumbs = [
    [$cluster->getShortClusterName() => '/'],
    ['namespaces' => '/namespaces'],
    [$namespace => namespaceUrl($namespace)],
    ['deployments' => '/deployments?' . \http_build_query(['namespace' => $namespace])],
    [$deployment  => null],
];

?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
    <div>
        <pre>
<?= h($deploymentDescription) ?>
        </pre>
    </div>
<?php DefaultLayout::close(); ?>
