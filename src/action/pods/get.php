<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$cluster = getCluster();
$namespace = $_GET['namespace'] ?? null;
$pods = $cluster->getPods($namespace);

?>

<?php DefaultLayout::open('Pods'); ?>
    <div>
        <ul>
            <?php foreach ($pods as $pod): ?>
                <li>
                    <?= h($pod) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php DefaultLayout::close(); ?>