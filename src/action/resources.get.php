<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;
use App\Route;

$kubernetes = getKubernetes();
$context = $_GET['context'] ?? die('No context specified');
$namespace = $_GET['namespace'] ?? null;
$objectKind = $_GET['kind'] ?? die('No object kind specified');
$objectKind = ObjectKind::from($objectKind);

$title = $objectKind->pluralTitle();

if ($namespace === null) {
    $objects = $kubernetes->getObjectsWithNamespaces($context, $objectKind);
    $breadcrumbs = [
        Route::forHome()->toBreadcrumb(),
        Route::forContext($context)->toBreadcrumb(),
        [$objectKind->pluralSmallTitle() => null],
    ];
} else {
    $objects = \array_map(
        fn ($obj) => [$objectKind->smallTitle() => $obj, 'namespace' => $namespace],
        $kubernetes->getObjects($context, $objectKind, $namespace),
    );
    $breadcrumbs = [
        Route::forHome()->toBreadcrumb(),
        Route::forContext($context)->toBreadcrumb(),
        Route::forNamespaces($context)->toBreadcrumb(),
        Route::forNamespace($context, $namespace)->toBreadcrumb(),
        [$objectKind->pluralSmallTitle() => null],
    ];
}
?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
<div>
    <table>
        <br>
        <?php if ($namespace === null && $objectKind->isNamespaced()): ?>
            <thead>
            <tr>
                <td><b>Namespace</b></td>
                <td><b><?= h($objectKind->title()) ?></b></td>
            </tr>
            </thead>
        <?php endif; ?>

        <tbody>
        <?php foreach ($objects as $object): ?>
            <tr>
                <?php if ($namespace === null && $objectKind->isNamespaced()): ?>
                    <td>
                        <a href="<?= Route::forNamespace($context, $object['namespace']) ?>">
                            <?= h($object['namespace']) ?>
                        </a>
                    </td>
                <?php endif; ?>

                <td>
                    <?php
                        if ($objectKind->isNamespaced()) {
                            $route = Route::forNamespacedResource(
                                $context,
                                $objectKind,
                                $object[$objectKind->smallTitle()],
                                $object['namespace'],
                            );
                        } else {
                            $route = Route::forNonNamespacedResource(
                                $context,
                                $objectKind,
                                $object[$objectKind->smallTitle()],
                            );
                        }
                    ?>

                    <a href="<?= $route ?>"><?= h($object[$objectKind->smallTitle()]) ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DefaultLayout::close(); ?>
