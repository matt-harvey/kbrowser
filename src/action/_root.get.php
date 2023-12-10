<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\Route;

$kubernetes = getKubernetes();
$title = 'KBrowser';
$breadcrumbs = [
    [HOME_CHAR => null],
];
$contexts = $kubernetes->getContexts();
?>

<?php DefaultLayout::use($title, $breadcrumbs) ?>

<?php foreach ($contexts as $context): ?>
    <p><a href="<?= Route::forContext($context) ?>"><?= h($context) ?></a></p>
<?php endforeach; ?>