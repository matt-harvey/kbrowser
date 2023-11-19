<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;

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
        fn ($d) => ['daemonset' => $d, 'namespace' => $namespace],
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
                            <a href="<?= namespacedResourceUrl(ObjectKind::DAEMON_SET, $daemonSet['daemonset'], $daemonSet['namespace']) ?>">
                                <?= h($daemonSet['daemonset']) ?>
                            </a>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php DefaultLayout::close(); ?>
