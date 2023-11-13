<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespace = $_GET['namespace'] ?? null;

$title = 'Deployments';

if ($namespace === null) {
    $deployments = $cluster->getDeploymentsWithNamespaces();
    $breadcrumbs = [
        [$cluster->getShortClusterName() => '/'],
        ['deployments' => null],
    ];
} else {
    $deployments = \array_map(
        fn ($d) => ['deployment' => $d, 'namespace' => $namespace],
        $cluster->getDeployments($namespace),
    );
    $breadcrumbs = [
        [$cluster->getShortClusterName() => '/'],
        ['namespaces' => '/namespaces'],
        [$namespace => namespaceUrl($namespace)],
        ['deployments' => null],
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
                        <td><b>Deployment</b></td>
                    </tr>
                </thead>
            <?php endif; ?>

            <tbody>
                <?php foreach ($deployments as $deployment): ?>
                    <tr>
                        <?php if ($namespace === null): ?>
                            <td>
                                <a href="<?= namespaceUrl($deployment['namespace']) ?>">
                                    <?= h($deployment['namespace']) ?>
                                </a>
                            </td>
                        <?php endif; ?>

                        <td>
                            <a href="<?= deploymentUrl($deployment['deployment'], $deployment['namespace']) ?>">
                                <?= h($deployment['deployment']) ?>
                            </a>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php DefaultLayout::close(); ?>
