<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespace = $_GET['namespace'] ?? null;

$title = 'StatefulSets';

if ($namespace === null) {
    $statefulsets = $cluster->getStatefulSetsWithNamespaces();
    $breadcrumbs = [
        [$cluster->getShortClusterName() => '/'],
        ['statefulsets' => null],
    ];
} else {
    $statefulsets = \array_map(
        fn ($d) => ['statefulSet' => $d, 'namespace' => $namespace],
        $cluster->getStatefulSets($namespace),
    );
    $breadcrumbs = [
        [$cluster->getShortClusterName() => '/'],
        ['namespaces' => '/namespaces'],
        [$namespace => namespaceUrl($namespace)],
        ['statefulsets' => null],
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
                        <td><b>StatefulSet</b></td>
                    </tr>
                </thead>
            <?php endif; ?>

            <tbody>
                <?php foreach ($statefulsets as $statefulSet): ?>
                    <tr>
                        <?php if ($namespace === null): ?>
                            <td>
                                <a href="<?= namespaceUrl($statefulSet['namespace']) ?>">
                                    <?= h($statefulSet['namespace']) ?>
                                </a>
                            </td>
                        <?php endif; ?>

                        <td>
                            <a href="<?= statefulSetUrl($statefulSet['statefulSet'], $statefulSet['namespace']) ?>">
                                <?= h($statefulSet['statefulSet']) ?>
                            </a>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php DefaultLayout::close(); ?>
