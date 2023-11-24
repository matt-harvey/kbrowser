<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;

$cluster = getKubernetes();
$title = 'KBrowser';
$context = $_GET['context'] ?? die('Context not provided');
$breadcrumbs = [
    [HOME_CHAR => rootUrl()],
    [simplifiedContextName($context) => null],
];
?>

<?php DefaultLayout::open($title, $breadcrumbs) ?>
    <?php foreach (ObjectKind::cases() as $objectKind): ?>

        <?php
            [$url, $linkText] = match ($objectKind) {
                ObjectKind::NAMESPACE => [namespacesUrl($context), 'Namespaces'],
                default => [resourcesUrl($context, $objectKind), $objectKind->pluralTitle()],
            };
        ?>

        <p>
            <a href="<?= $url ?>"><?= h($linkText) ?></a>
        </p>

    <?php endforeach; ?>
<?php DefaultLayout::close(); ?>
