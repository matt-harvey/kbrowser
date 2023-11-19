<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;

$cluster = getCluster();
$namespace = $_GET['namespace'] ?? null;

$title = 'Pods';

if ($namespace === null) {
    $pods = $cluster->getPodsWithNamespaces();
    $breadcrumbs = [
        [$cluster->getShortClusterName() => '/'],
        ['pods' => null],
    ];
} else {
    $pods = \array_map(
        fn ($p) => ['pod' => $p, 'namespace' => $namespace],
        $cluster->getPods($namespace),
    );
    $breadcrumbs = [
        [$cluster->getShortClusterName() => '/'],
        ['namespaces' => '/namespaces'],
        [$namespace => namespaceUrl($namespace)],
        ['pods' => null],
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
                <td><b>Pod</b></td>
            </tr>
            </thead>
        <?php endif; ?>

        <tbody>
        <?php foreach ($pods as $pod): ?>
            <tr>
                <?php if ($namespace === null): ?>
                    <td>
                        <a href="<?= namespaceUrl($pod['namespace']) ?>">
                            <?= h($pod['namespace']) ?>
                        </a>
                    </td>
                <?php endif; ?>

                <td>
                    <a href="<?= namespacedResourceUrl(ObjectKind::POD, $pod['pod'], $pod['namespace']) ?>">
                        <?= h($pod['pod']) ?>
                    </a>

                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DefaultLayout::close(); ?>
