<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$title = 'KBrowser';
$breadcrumbs = [
    [$cluster->getShortClusterName() => null],
];
?>

<?php DefaultLayout::open($title, $breadcrumbs) ?>
    <p>
        <a href="/namespaces">Namespaces</a>
    </p>
    <p>
        <a href="/pods">Pods</a>
    </p>
    <p>
        <a href="/deployments">Deployments</a>
    </p>
<?php DefaultLayout::close(); ?>
