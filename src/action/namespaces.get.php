<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\Route;

$kubernetes = getKubernetes();
$context = $_GET['context'] ?? die('Context not provided');
$namespaces = $kubernetes->getNamespaces($context);

$title = 'Namespaces';
$breadcrumbs = [
    Route::forHome()->toBreadcrumb(),
    Route::forContext($context)->toBreadcrumb(),
    ['namespaces' => null],
];

?>

<?php DefaultLayout::open($title, $breadcrumbs) ?>
    <div>
        <table>
            <tbody>
                <?php foreach ($namespaces as $namespace): ?>
                    <tr>
                        <td>
                            <a href="<?= Route::forNamespace($context, $namespace) ?>">
                                <?= h($namespace) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php DefaultLayout::close(); ?>