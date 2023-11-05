<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

?>

<?php DefaultLayout::open('Namespaces'); ?>
    <div>
        Current path: <?= h(PATH) ?>
    </div>
<?php DefaultLayout::close(); ?>
