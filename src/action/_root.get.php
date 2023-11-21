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

        <?php
            [$url, $linkText] = match ($objectKind) {
                ObjectKind::NAMESPACE => [namespacesUrl(), 'Namespaces'],
                default => [resourcesUrl($objectKind), $objectKind->pluralTitle()],
            };
        ?>

        <p>
            <a href="<?= $url ?>"><?= h($linkText) ?></a>
        </p>

    <?php endforeach; ?>
<?php DefaultLayout::close(); ?>
