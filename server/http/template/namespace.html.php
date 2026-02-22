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
 * @var string $namespace
 * @var string $namespaceDescription
 * @var string $context
 * @var ?string $errorMessage
 */
?>

<?php if (DefaultLayout::open($this, $title, $breadcrumbs, $errorMessage)): ?>

    <div>
    <pre>
<?= $this->e($namespaceDescription) ?>
    </pre>
    </div>

    <hr>

    <?php foreach (ObjectKind::cases() as $resourceType): ?>
        <?php if ($resourceType->isNamespaced()): ?>
            <p>
                <?= $this->e($namespace) ?> &rightarrow;

                <a href="<?= Route::forResources($context, $resourceType, $namespace) ?>">
                    <?= $this->e($resourceType->pluralTitle()) ?>
                </a>
            </p>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php DefaultLayout::close() ?>
