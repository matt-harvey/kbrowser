<?php

namespace App\Layout;

class DefaultLayout
{
    /** @param array<array<string, string|null>> $breadcrumbs */
    public static function open(string $title, array $breadcrumbs = []): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title><?= h($title) ?></title>
            <meta charset="utf8">
            <style>
                * {
                    font-family: monospace;
                }
                body {
                    line-height: 1.25em;
                }
                pre {
                    line-height: 1.125em;
                }
                a {
                    text-decoration: none;
                    color: green;
                }
                table {
                    border-spacing: 1em 0;
                }
            </style>
        </head>

        <body>

        <?php if (\count($breadcrumbs) != 0): ?>
            <nav>
                <?php $i = 0; ?>
                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                    <?php $label = \array_keys($breadcrumb)[0]; ?>
                    <?php $url = \array_values($breadcrumb)[0]; ?>

                    <?php if ($i != 0): ?> &rightarrow; <?php endif; ?>
                    <?php if ($url === null): ?>
                        <?= h($label) ?>
                    <?php else: ?>
                        <a href="<?= $url ?>"><?= h($label) ?></a>
                    <?php endif; ?>

                    <?php $i++; ?>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <hr>

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