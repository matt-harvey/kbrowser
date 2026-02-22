<?php

declare(strict_types=1);

namespace App\Html\Layout;

use App\Html\Breadcrumb;
use SubstancePHP\HTTP\Renderer\HtmlRenderer;

abstract class DefaultLayout
{
    /** @param array<Breadcrumb> $breadcrumbs */
    public static function open(
        HtmlRenderer $r,
        string $title,
        array $breadcrumbs = [],
        ?string $errorMessage = null,
    ): bool { ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title><?= $r->e($title) ?></title>
            <meta charset="UTF-8">
            <link rel="icon" href="data:,">
            <style>
                * {
                    color: #222;
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
                    color: darkcyan;
                }
                button {
                    background-color: lightgoldenrodyellow;
                    border-color: deepskyblue;
                }
                button a {
                    color: black;
                }
                table {
                    border-spacing: 2em 0.25em;
                }
                b {
                    color: black;
                }
            </style>

        </head>
        <body>

            <?php if (\count($breadcrumbs) != 0): ?>
                <nav>
                    <?php $i = 0; ?>
                    <?php foreach ($breadcrumbs as $breadcrumb): ?>
                        <?php if ($i != 0): ?> &rightarrow; <?php endif; ?>
                        <?php if ($breadcrumb->url === null): ?>
                            <?= $r->e($breadcrumb->title) ?>
                        <?php else: ?>
                            <a href="<?= $breadcrumb->url ?>"><?= $r->e($breadcrumb->title) ?></a>
                        <?php endif; ?>

                        <?php $i++; ?>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <hr>

            <?php if ($errorMessage !== null): ?>
                <p><?= $r->e($errorMessage) ?></p>
                <?php return false; ?>
            <?php endif; /** @phpstan-ignore deadCode.unreachable */ ?>
        <?php return true;
    }

    public static function close(): void
    {
        ?>
            </body>
            </html>
        <?php
    }
}