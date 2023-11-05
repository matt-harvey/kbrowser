<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

define('PATH', \parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

function main(): void
{
    $urlPath = (PATH === '/' ? '_root' : PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    $lowerMethod = \strtolower($method);
    $action = ACTION_ROOT . "/$urlPath.$lowerMethod.php";
    if (!\file_exists($action)) {
        \http_response_code(404);
        return;
    }
    require $action;
}

main();