<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespace = $_GET['namespace'] ?? null;

$title = 'DaemonSets';

if ($namespace === null) {
    $daemonsets = $cluster->getDaemonSetsWithNamespaces();
    $breadcrumbs = [
        [$cluster->getShortClusterName() => '/'],
        ['daemonsets' => null],
    ];
} else {
    $daemonsets = \array_map(
        fn ($d) => ['daemonSet' => $d, 'namespace' => $namespace],
        $cluster->getDaemonSets($namespace),
    );
    $breadcrumbs = [
        [$cluster->getShortClusterName() => '/'],
        ['namespaces' => '/namespaces'],
        [$namespace => namespaceUrl($namespace)],
        ['daemonsets' => null],
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
                        <td><b>DaemonSet</b></td>
                    </tr>
                </thead>
            <?php endif; ?>

            <tbody>
                <?php foreach ($daemonsets as $daemonSet): ?>
                    <tr>
                        <?php if ($namespace === null): ?>
                            <td>
                                <a href="<?= namespaceUrl($daemonSet['namespace']) ?>">
                                    <?= h($daemonSet['namespace']) ?>
                                </a>
                            </td>
                        <?php endif; ?>

                        <td>
                            <a href="<?= daemonSetUrl($daemonSet['daemonSet'], $daemonSet['namespace']) ?>">
                                <?= h($daemonSet['daemonSet']) ?>
                            </a>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php DefaultLayout::close(); ?>
