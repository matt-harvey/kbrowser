<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;

$kubernetes = getKubernetes();
$context = $_GET['context'] or die('No context specified');
$objectKind = $_GET['kind'] or die('No object kind specified');
$objectKind = ObjectKind::from($objectKind);
$objectName = $_GET['object'] or die('No object specified');
$objectDescription = $kubernetes->describe(
        $context,
        $objectKind,
        null,
        $objectName,
);

$title = $objectName;
$breadcrumbs = [
    [HOME_CHAR => rootUrl()],
    [simplifiedContextName($context) => contextUrl($context)],
    [$objectKind->pluralSmallTitle() => resourcesUrl($context, $objectKind, null)],
    [$objectName  => null],
];
?>

<?php DefaultLayout::open($title, $breadcrumbs); ?>
    <div>
        <pre>
<?= h($objectDescription) ?>
        </pre>
    </div>
<?php DefaultLayout::close(); ?>
