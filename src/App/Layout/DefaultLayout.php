<?php

namespace App\Layout;

class DefaultLayout
{
    /** @param array<string, string> $breadcrumbs */
    public static function open(string $title, array $breadcrumbs = []): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title><?= h($title) ?></title>
            <meta charset="utf8">
        </head>

        <body>

        <?php if (\count($breadcrumbs) != 0): ?>
            <nav>
                <?php $i = 0; ?>
                <?php foreach ($breadcrumbs as $label => $url): ?>
                    <?php if ($i != 0): ?> / <?php endif; ?>
                    <?php if ($url === null): ?>
                        <?= h($label) ?>
                    <?php else: ?>
                        <a href="<?= $url ?>"><?= h($label) ?></a>
                    <?php endif; ?>

                    <?php $i++; ?>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

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