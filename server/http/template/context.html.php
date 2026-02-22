<?php

declare(strict_types=1);

use App\Enum\ObjectKind;
use SubstancePHP\HTTP\Renderer\HtmlRenderer;
use App\Html\Layout\DefaultLayout;
use App\Route;
use App\Html\Breadcrumb;

/**
 * @var HtmlRenderer $this
 * @var string $title
 * @var Breadcrumb[] $breadcrumbs
 * @var string $context
 */
?>

<?php if (DefaultLayout::open($this, $title, $breadcrumbs)): ?>
    <?php foreach (ObjectKind::cases() as $objectKind): ?>

        <?php
        [$url, $linkText] = match ($objectKind) {
            ObjectKind::NAMESPACE => [Route::forNamespaces($context)->toUrl(), 'Namespaces'],
            default => [Route::forResources($context, $objectKind)->toUrl(), $objectKind->pluralTitle()],
        };
        ?>

        <p>
            <a href="<?= $url ?>"><?= $this->e($linkText) ?></a>
        </p>
    <?php endforeach; ?>
<?php endif; ?>
<?php DefaultLayout::close() ?>
