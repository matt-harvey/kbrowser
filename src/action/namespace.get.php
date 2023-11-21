<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;

$cluster = getCluster();
$namespace = $_GET['namespace'] or die('Namespace not specified');
$namespaceDescription = $cluster->describe(ObjectKind::NAMESPACE, null, $namespace);

$title = 'Namespaces';
$breadcrumbs = [
    [$cluster->getShortClusterName() => '/'],
    ['namespaces' => namespacesUrl()],
    [$namespace => null],
];

?>

<?php DefaultLayout::open($title, $breadcrumbs) ?>
    <div>
        <pre>
<?= h($namespaceDescription) ?>
        </pre>
    </div>

    <hr>

    <?php foreach (ObjectKind::cases() as $resourceType): ?>
        <?php if ($resourceType->isNamespaced()): ?>
            <p>
                <?= h($namespace) ?> &rightarrow;
                <a href="<?= resourcesUrl($resourceType, $namespace) ?>">
                    <?= h($resourceType->pluralTitle()) ?>
                </a>
            </p>
        <?php endif; ?>
    <?php endforeach; ?>
<?php DefaultLayout::close(); ?>
