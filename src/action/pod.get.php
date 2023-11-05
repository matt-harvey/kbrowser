<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespace = $_GET['namespace'] ?? $cluster->getCurrentNamespace();
$pod = $_GET['pod'] or die('No pod specified');
$podDescription = $cluster->describePod($namespace, $pod);

$title = $pod;
$breadcrumbs = [
    'home' => '/',
    'namespaces' => '/namespaces',
    $namespace => '/pods?' . \http_build_query(['namespace' => $namespace]),
    'pods' => null,
    simplifiedPodName($pod) => null,
];

?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
    <div>
        <pre>
            <?= h($podDescription) ?>
        </pre>
    </div>
<?php DefaultLayout::close(); ?>