<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;

$cluster = getCluster();
$title = 'KBrowser';
$breadcrumbs = [
    [$cluster->getShortClusterName() => null],
];
?>

<?php DefaultLayout::open($title, $breadcrumbs) ?>
    <?php foreach (ObjectKind::cases() as $objectKind): ?>
        <?php if ($objectKind === ObjectKind::NAMESPACE): ?>
            <p>
                <a href="<?= namespacesUrl() ?>">Namespaces</a>
            </p>
        <?php else: ?>
            <p>
                <a href="<?= resourcesUrl($objectKind) ?>">
                    <?= h($objectKind->pluralTitle()) ?>
                </a>
            </p>
        <?php endif; ?>
    <?php endforeach; ?>
<?php DefaultLayout::close(); ?>
