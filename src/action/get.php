<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

?>

<?php DefaultLayout::open('Kbrowser'); ?>
    <div>
        <nav>
            <ul>
                <li><a href="/namespaces">Namespaces</a></li>
                <li><a href="/pods">Pods</a></li>
            </ul>
        </nav>
    </div>
<?php DefaultLayout::close(); ?>
