<?php

declare(strict_types=1);

use App\Layout\DefaultLayout;
use App\ObjectKind;

$kubernetes = getKubernetes();
$context = $_GET['context'] or die('No context specified');
$namespace = $_GET['namespace'] or die('No namespace specified');
$objectKind = $_GET['kind'] or die('No object kind specified');
$objectKind = ObjectKind::from($objectKind);
$objectName = $_GET['object'] or die('No object specified');
$objectDescription = $kubernetes->describe($context, $objectKind, $namespace, $objectName);

$title = $objectName;
$breadcrumbs = [
    [HOME_CHAR => rootUrl()],
    [simplifiedContextName($context) => contextUrl($context)],
    ['namespaces' => namespacesUrl($context)],
    [$namespace => namespaceUrl($context, $namespace)],
    [$objectKind->pluralSmallTitle() => resourcesUrl($context, $objectKind, $namespace)],
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
