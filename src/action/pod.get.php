<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespace = $_GET['namespace'] or die('No namespace specified');
$pod = $_GET['pod'] or die('No pod specified');
$podDescription = $cluster->describePod($pod, $namespace);

$title = $pod;
$breadcrumbs = [
    [$cluster->getShortClusterName() => '/'],
    ['namespaces' => '/namespaces'],
    [$namespace => namespaceUrl($namespace)],
    ['pods' => '/pods?' . \http_build_query(['namespace' => $namespace])],
    [$pod  => null],
];
?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
    <div>
        <pre>
<?= h($podDescription) ?>
        </pre>
    </div>
<?php DefaultLayout::close(); ?>