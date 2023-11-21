<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;

$cluster = getCluster();
$namespace = $_GET['namespace'] ?? null;
$objectKind = $_GET['kind'] ?? die('No object kind specified');
$objectKind = ObjectKind::from($objectKind);

$title = $objectKind->pluralTitle();

if ($namespace === null) {
    $objects = $cluster->getObjectsWithNamespaces($objectKind);
    $breadcrumbs = [
        [$cluster->getShortClusterName() => '/'],
        [$objectKind->pluralSmallTitle() => null],
    ];
} else {
    $objects = \array_map(
        fn ($obj) => [$objectKind->smallTitle() => $obj, 'namespace' => $namespace],
        $cluster->getObjects($objectKind, $namespace),
    );
    $breadcrumbs = [
        [$cluster->getShortClusterName() => '/'],
        ['namespaces' => '/namespaces'],
        [$namespace => namespaceUrl($namespace)],
        [$objectKind->pluralSmallTitle() => null],
    ];
}
?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
<div>
    <table>
        <br>
        <?php if ($namespace === null && $objectKind->isNamespaced()): ?>
            <thead>
            <tr>
                <td><b>Namespace</b></td>
                <td><b><?= h($objectKind->title()) ?></b></td>
            </tr>
            </thead>
        <?php endif; ?>

        <tbody>
        <?php foreach ($objects as $object): ?>
            <tr>
                <?php if ($namespace === null && $objectKind->isNamespaced()): ?>
                    <td>
                        <a href="<?= namespaceUrl($object['namespace']) ?>">
                            <?= h($object['namespace']) ?>
                        </a>
                    </td>
                <?php endif; ?>

                <td>
                    <?php if ($objectKind->isNamespaced()): ?>
                        <a href="<?= namespacedResourceUrl($objectKind, $object[$objectKind->smallTitle()], $object['namespace']) ?>">
                            <?= h($object[$objectKind->smallTitle()]) ?>
                        </a>
                    <?php else: ?>
                        <a href="<?= nonNamespacedResourceUrl($objectKind, $object[$objectKind->smallTitle()]) ?>">
                            <?= h($object[$objectKind->smallTitle()]) ?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DefaultLayout::close(); ?>
