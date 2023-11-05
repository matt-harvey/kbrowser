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
        <h1><?= h($title) ?></h1>
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