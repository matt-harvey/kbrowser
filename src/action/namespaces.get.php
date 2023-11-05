<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespaces = $cluster->getNamespaces();

$title = 'Namespaces';
$breadcrumbs = [
    'home' => '/',
    'namespaces' => null,
];

?>

<?php DefaultLayout::open($title, $breadcrumbs) ?>
    <div>
        <ul>
            <?php foreach ($namespaces as $namespace): ?>
                <li>
                    <a href="<?= '/pods?' . \http_build_query(['namespace' => $namespace]) ?>">
                        <?= h($namespace) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php DefaultLayout::close(); ?>