<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ResourceType;

$cluster = getCluster();
$namespace = $_GET['namespace'] or die('Namespace not specified');

$title = 'Namespaces';
$breadcrumbs = [
    [$cluster->getShortClusterName() => '/'],
    ['namespaces' => resourcesUrl(ResourceType::NAMESPACE)],
    [$namespace => null],
];

?>

<?php DefaultLayout::open($title, $breadcrumbs) ?>
    <?php foreach (ResourceType::cases() as $resourceType): ?>
        <?php if ($resourceType !== ResourceType::NAMESPACE): ?>
            <p>
                <a href="<?= resourcesUrl($resourceType, $namespace) ?>">
                    <?= h($resourceType->pluralTitle()) ?>
                </a>
            </p>
        <?php endif; ?>
    <?php endforeach; ?>
<?php DefaultLayout::close(); ?>
