<?php

namespace App\Layout;

class DefaultLayout
{
    public static function open(string $title): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title><?= h($title) ?></title>
            <meta charset="utf8">
        </head>

        <body>
        <?php
    }

    public static function close(): void
    {
        ?>
        </body>
        </html>
        <?php
    }
}