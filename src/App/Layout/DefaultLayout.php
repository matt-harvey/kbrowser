<?php

namespace App\Layout;

class DefaultLayout
{
    /** @param array<array<string, string|null>> $breadcrumbs */
    public static function use(
            string $title,
            array $breadcrumbs = [],
            int $statusCode = 200,
            ?string $errorMessage = null,
    ): self
    {
        return new self($title, $breadcrumbs, $statusCode, $errorMessage);
    }

    /** @param array<array<string, string|null>> $breadcrumbs */
    private function __construct(string $title, array $breadcrumbs, int $statusCode, ?string $errorMessage)
    {
        \header("content-type: text/html; charset=UTF-8");
        \http_response_code($statusCode);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title><?= h($title) ?></title>
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

        <?php if ($errorMessage !== null): ?>
            <p>
                <?= h($errorMessage) ?>
            </p>
            <?php exit; ?>
        <?php endif; /** @phpstan-ignore-line */ ?>

        <?php
    }

    public function __destruct()
    {
        ?>
        </body>
        </html>
        <?php
    }
}