<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespaces = $cluster->getNamespaces();

$title = 'Namespaces';
$breadcrumbs = [
    [$cluster->getShortClusterName() => '/'],
    ['namespaces' => null],
];

?>

<?php DefaultLayout::open($title, $breadcrumbs) ?>
    <div>
        <table>
            <tbody>
                <?php foreach ($namespaces as $namespace): ?>
                    <tr>
                        <td>
                            <a href="<?= namespaceUrl($namespace) ?>">
                                <?= h($namespace) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php DefaultLayout::close(); ?>