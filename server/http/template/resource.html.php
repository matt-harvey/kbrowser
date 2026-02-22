<?php

declare(strict_types=1);

use App\Enum\ObjectKind;
use App\Html\Breadcrumb;
use App\Html\Layout\DefaultLayout;
use App\Route;
use SubstancePHP\HTTP\Renderer\HtmlRenderer;

/**
 * @var HtmlRenderer $this
 * @var ?string $errorMessage
 * @var ?string $ownerKindStr
 * @var ?string $ownerName
 * @var ?string $ownerUrl
 * @var Breadcrumb[] $breadcrumbs
 * @var ObjectKind $objectKind
 * @var string $context
 * @var string $namespace
 * @var string $objectDescription
 * @var string $objectName
 * @var string $title
 * @var string[] $selectors
 */

?>
<?php if (DefaultLayout::open($this, $title, $breadcrumbs, $errorMessage)): ?>

    <b><?= $this->e("{$objectKind->value}/$objectName") ?></b>
    <?php if ($ownerUrl !== null): ?>
        [controlled by <a href="<?= $ownerUrl ?>"><?= $this->e("$ownerKindStr/$ownerName") ?></a>]
    <?php endif; ?>

    <?php if ($objectKind === ObjectKind::POD || \count($selectors) != 0): ?>
        <div>
            <div style="border: 1px solid burlywood; padding: 5px; margin: 1em 0 1em 0; background-color: #f9f9f9; display: inline-block;">
                <?php if ($objectKind === ObjectKind::POD): ?>
                    <div style="margin-top: 0.25em">
                        Logs for pod:
                        <a href="<?= Route::forPodLogs($context, $namespace, $objectName, true) ?>">
                            <?= $this->e($objectName) ?>
                        </a>
                        <br>
                    </div>
                <?php endif; ?>

                <?php foreach ($selectors as $selector): ?>
                    <div style="margin-top: 0.25em">
                        Logs for selector:
                        <a href="<?= Route::forSelectorLogs($context, $namespace, $selector, true) ?>">
                            <?= $this->e($selector) ?>
                        </a>
                        <br>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div>
        <pre>
    <?= $this->e($objectDescription) ?>
        </pre>
    </div>
<?php endif; ?>
<?php DefaultLayout::close(); ?>
