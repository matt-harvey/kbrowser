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
        <?php if ($namespace === null): ?>
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
                <?php if ($namespace === null): ?>
                    <td>
                        <a href="<?= namespaceUrl($object['namespace']) ?>">
                            <?= h($object['namespace']) ?>
                        </a>
                    </td>
                <?php endif; ?>

                <td>
                    <a href="<?= namespacedResourceUrl($objectKind, $object[$objectKind->smallTitle()], $object['namespace']) ?>">
                        <?= h($object[$objectKind->smallTitle()]) ?>
                    </a>

                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DefaultLayout::close(); ?>
