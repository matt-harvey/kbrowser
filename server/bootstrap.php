<?php

declare(strict_types=1);

\define('SRC_ROOT', __DIR__);
\define('PROJECT_ROOT', \dirname(SRC_ROOT));
\define('HTTP_ROOT', SRC_ROOT . '/http');
\define('HTTP_ACTION_ROOT', HTTP_ROOT . '/action');
\define('HTTP_TEMPLATE_ROOT', HTTP_ROOT . '/template');
\define('HOME_CHAR', \json_decode('"\u2302"'));

require PROJECT_ROOT . '/vendor/autoload.php';

use SubstancePHP\HTTP\Exception\BaseException\UserError;

\date_default_timezone_set('UTC');

\error_reporting(\E_ALL | \E_STRICT);

\set_exception_handler(function (\Throwable $exception): void {
    if ($exception instanceof UserError) {
        switch (\php_sapi_name()) {
            case 'cli':
                \error_log($exception->getMessage());
                return;
            default:
                \http_response_code($exception->getStatusCode());
                echo 'Error: ' . $exception->getMessage();
                return;
        }
    }
    \error_log(\get_class($exception) . ': ' . $exception->getMessage());
    \error_log($exception->getTraceAsString());
    if (\php_sapi_name() !== 'cli') {
        \http_response_code(500);
        echo 'Error: something went wrong';
    }
});

\set_error_handler(
    function (int $errno, string $errstr, string $errfile = '', int $errline = 0): bool {
        // Skip errors suppressed by '@'
        if (! (\error_reporting() & $errno)) {
            return true;
        }
        throw new \ErrorException(
            message: $errstr,
            code: 0,
            severity: $errno,
            filename: $errfile,
            line: $errline,
            previous: null,
        );
    }
);

// global helper functions // FIXNOW Get rid of these

function simplifiedObjectName(string $fullObjectName): string
{
    return \preg_replace('|^.+/|', '', $fullObjectName);
}

function simplifiedContextName(string $fullName): string
{
    return \preg_replace('|^.+/|', '', $fullName);
}
