<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$kubernetes = getKubernetes();
$title = 'KBrowser';
$breadcrumbs = [
    [HOME_CHAR => null],
];
$contexts = $kubernetes->getContexts();
?>

<?php DefaultLayout::open($title, $breadcrumbs) ?>
    <?php foreach ($contexts as $context): ?>

        <p>
            <a href="<?= contextUrl($context) ?>"><?= h($context) ?></a>
        </p>

    <?php endforeach; ?>
<?php DefaultLayout::close(); ?>
