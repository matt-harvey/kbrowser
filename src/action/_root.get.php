<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ResourceType;

$cluster = getCluster();
$title = 'KBrowser';
$breadcrumbs = [
    [$cluster->getShortClusterName() => null],
];
?>

<?php DefaultLayout::open($title, $breadcrumbs) ?>
    <?php foreach (ResourceType::cases() as $resourceType): ?>
        <p>
            <a href="<?= resourcesUrl($resourceType) ?>">
                <?= h($resourceType->pluralTitle()) ?>
            </a>
        </p>
    <?php endforeach; ?>
<?php DefaultLayout::close(); ?>
