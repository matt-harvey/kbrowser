<?php

declare(strict_types=1);

use App\Enum\ObjectKind;
use App\Html\Breadcrumb;
use App\Html\Layout\DefaultLayout;
use App\Route;
use SubstancePHP\HTTP\Renderer\HtmlRenderer;

/**
 * @var HtmlRenderer $this
 * @var string $title
 * @var Breadcrumb[] $breadcrumbs
 * @var ?string $errorMessage
 * @var string $objectDescription
 */

?>
<?php if (DefaultLayout::open($this, $title, $breadcrumbs, $errorMessage)): ?>
    <div><pre>
<?= $this->e($objectDescription) ?>
    </pre></div>
<?php endif; ?>
<?php DefaultLayout::close(); ?>
