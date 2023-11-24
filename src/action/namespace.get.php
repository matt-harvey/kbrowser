<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;

$kubernetes = getKubernetes();
$context = $_GET['context'] or die('Context not specified');
$namespace = $_GET['namespace'] or die('Namespace not specified');
$namespaceDescription = $kubernetes->describe(
        $context,
        ObjectKind::NAMESPACE,
        null,
        $namespace,
);

$title = 'Namespaces';
$breadcrumbs = [
    [HOME_CHAR => rootUrl()],
    [simplifiedContextName($context) => contextUrl($context)],
    ['namespaces' => namespacesUrl($context)],
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

                <a href="<?= resourcesUrl($context, $resourceType, $namespace) ?>">
                    <?= h($resourceType->pluralTitle()) ?>
                </a>
            </p>
        <?php endif; ?>
    <?php endforeach; ?>
<?php DefaultLayout::close(); ?>
