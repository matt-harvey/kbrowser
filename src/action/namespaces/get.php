<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespaces = $cluster->getNamespaces();
$currentNamespace = $cluster->getCurrentNamespace();

?>

<?php DefaultLayout::open('Namespaces'); ?>
    <div>
        <ul>
            <?php foreach ($namespaces as $namespace): ?>
                <li>
                    <a href="<?= '/pods?' . \http_build_query(['namespace' => $namespace]) ?>">
                        <?php if ($namespace === $currentNamespace): ?>
                            <b><?= h($namespace) ?></b>
                        <?php else: ?>
                            <?= h($namespace) ?>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php DefaultLayout::close(); ?>