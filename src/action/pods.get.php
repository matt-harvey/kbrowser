<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespace = $_GET['namespace'] ?? $cluster->getCurrentNamespace();
$pods = $cluster->getPods($namespace);

$title = 'Pods';
$breadcrumbs = [
    'home' => '/',
    'namespaces' => '/namespaces',
    $namespace => null,
    'pods' => null,
];

?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
    <div>
        <ul>
            <?php foreach ($pods as $pod): ?>
                <li>
                    <a href="<?= podUrl($namespace, $pod) ?>">
                        <?= h(simplifiedPodName($pod)) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php DefaultLayout::close(); ?>
