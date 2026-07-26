<?php

declare(strict_types=1);

use App\Html\Layout\DefaultLayout;
use SubstancePHP\HTTP\Renderer\HtmlRenderer;

/**
 * @var HtmlRenderer $this
 * @var string $file
 * @var int $line
 * @var string $exception
 * @var string $message
 * @var string[] $trace
 */

?>

<?php if (DefaultLayout::open($this, 'Error', [], $this->e("$file:$line $message"))): ?>
    <?php foreach ($trace as $traceLine): ?>
        <p><?= $this->e($traceLine) ?></p>
    <?php endforeach; ?>
<?php endif; ?>
<?php DefaultLayout::close() ?>
