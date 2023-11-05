<?php

declare(strict_types=1);

define('SRC_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(SRC_ROOT));
define('ACTION_ROOT', SRC_ROOT . '/action');

require PROJECT_ROOT . '/vendor/autoload.php';

// global helper functions

function h(mixed $s): string
{
    return htmlspecialchars($s);
}