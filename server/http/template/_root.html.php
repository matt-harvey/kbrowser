<?php

declare(strict_types=1);

use SubstancePHP\HTTP\Renderer\HtmlRenderer;
use App\Html\Layout\DefaultLayout;
use App\Route;
use App\Html\Breadcrumb;

/**
 * @var HtmlRenderer $this
 * @var string $title
 * @var Breadcrumb[] $breadcrumbs
 * @var string[] $contexts
 */

?>

<?php if (DefaultLayout::open($this, $title, $breadcrumbs)): ?>
    <?php foreach ($contexts as $context): ?>
        <p><a href="<?= Route::forContext($context) ?>"><?= $this->e($context) ?></a></p>
    <?php endforeach; ?>
<?php endif; ?>
<?php DefaultLayout::close() ?>
