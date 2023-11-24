<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;

$kubernetes = getKubernetes();
$context = $_GET['context'] ?? die('Context not provided');
$namespaces = $kubernetes->getNamespaces($context);

$title = 'Namespaces';
$breadcrumbs = [
    [HOME_CHAR => rootUrl()],
    [simplifiedContextName($context) => contextUrl($context)],
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
                            <a href="<?= namespaceUrl($context, $namespace) ?>">
                                <?= h($namespace) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php DefaultLayout::close(); ?>